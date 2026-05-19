<?php

namespace App\Services;

use App\Models\Doctor;
use App\Models\Rating;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class DoctorRatingDashboardService
{
    public function build(): array
    {
        $defaultSummary = [
            'averageDoctorRating' => null,
            'topRatedDoctor' => null,
            'needsAttentionCount' => 0,
            'totalReviews' => 0,
        ];

        $defaultCharts = [
            'overview' => [
                'labels' => [],
                'values' => [],
                'colors' => [],
            ],
            'distribution' => [
                'labels' => ['Excellent', 'Good', 'Needs Improvement', 'No Reviews'],
                'counts' => [0, 0, 0, 0],
                'percentages' => [0, 0, 0, 0],
            ],
            'trend' => [
                'labels' => [],
                'values' => [],
            ],
        ];

        if (! Schema::hasTable('ratings')) {
            $doctors = $this->baseDoctors()->get();

            return [
                'summary' => $defaultSummary + ['trackedDoctors' => $doctors->count()],
                'performanceRows' => $this->performanceRows($doctors),
                'ratingsRows' => $this->performanceRows($doctors),
                'charts' => $this->charts($doctors),
            ];
        }

        $doctors = $this->baseDoctors()
            ->addSelect([
                'latest_feedback_rating' => Rating::query()
                    ->select('rating')
                    ->whereColumn('ratings.doctor_id', 'doctors.id')
                    ->orderByDesc('ratings.created_at')
                    ->orderByDesc('ratings.id')
                    ->limit(1),
                'latest_feedback_comment' => Rating::query()
                    ->select('comment')
                    ->whereColumn('ratings.doctor_id', 'doctors.id')
                    ->orderByDesc('ratings.created_at')
                    ->orderByDesc('ratings.id')
                    ->limit(1),
            ])
            ->withCount(['ratings as reviews_count'])
            ->withAvg('ratings as avg_rating', 'rating')
            ->orderBy('name')
            ->get();

        $ratedDoctors = $doctors->filter(fn (Doctor $doctor) => $doctor->reviews_count > 0)->values();

        $summary = [
            'averageDoctorRating' => $ratedDoctors->isNotEmpty()
                ? round((float) $ratedDoctors->avg('avg_rating'), 1)
                : null,
            'topRatedDoctor' => $ratedDoctors
                ->sortByDesc(fn (Doctor $doctor) => ((float) $doctor->avg_rating * 1000) + (int) $doctor->reviews_count)
                ->first(),
            'needsAttentionCount' => $ratedDoctors
                ->filter(fn (Doctor $doctor) => (float) $doctor->avg_rating < 3)
                ->count(),
            'totalReviews' => (int) $doctors->sum('reviews_count'),
            'trackedDoctors' => $doctors->count(),
        ];

        $performanceRows = $this->performanceRows($doctors);

        return [
            'summary' => $summary,
            'performanceRows' => $performanceRows,
            'ratingsRows' => $performanceRows,
            'charts' => $this->charts($doctors),
        ];
    }

    private function baseDoctors()
    {
        return Doctor::query()
            ->withoutTrashed()
            ->with('department:id,name_en')
            ->orderBy('name');
    }

    private function performanceRows(Collection $doctors): Collection
    {
        return $doctors->map(function (Doctor $doctor) {
            $reviewsCount = (int) ($doctor->reviews_count ?? 0);
            $average = $reviewsCount > 0 ? (float) $doctor->avg_rating : 0.0;

            return [
                'name' => $doctor->name,
                'department' => $doctor->department?->name_en ?? 'Unassigned',
                'avg_rating' => round($average, 1),
                'reviews_count' => $reviewsCount,
                'latest_feedback_rating' => $doctor->latest_feedback_rating !== null
                    ? round((float) $doctor->latest_feedback_rating, 1)
                    : null,
                'latest_feedback_comment' => $doctor->latest_feedback_comment,
                'trend' => $this->buildTrend($reviewsCount),
                'status' => $this->statusFromAverage($average),
            ];
        });
    }

    private function charts(Collection $doctors): array
    {
        $ratedDoctors = $doctors
            ->filter(fn (Doctor $doctor) => (int) ($doctor->reviews_count ?? 0) > 0
                && $doctor->avg_rating !== null
                && filled($doctor->name))
            ->sortByDesc(fn (Doctor $doctor) => (float) $doctor->avg_rating)
            ->values();

        $distributionCounts = [
            'Excellent' => 0,
            'Good' => 0,
            'Needs Improvement' => 0,
            'No Reviews' => 0,
        ];

        foreach ($doctors as $doctor) {
            $distributionCounts[$this->distributionBucket((float) ($doctor->avg_rating ?? 0), (int) $doctor->reviews_count)]++;
        }

        $distributionTotal = array_sum($distributionCounts);

        return [
            'overview' => [
                'labels' => $ratedDoctors->pluck('name')->all(),
                'values' => $ratedDoctors->map(fn (Doctor $doctor) => round((float) ($doctor->avg_rating ?? 0), 2))->all(),
                'colors' => $ratedDoctors->map(fn (Doctor $doctor) => $this->colorForAverage((float) ($doctor->avg_rating ?? 0)))->all(),
            ],
            'distribution' => [
                'labels' => array_keys($distributionCounts),
                'counts' => array_values($distributionCounts),
                'percentages' => $distributionTotal > 0
                    ? array_map(
                        static fn (int $count) => round(($count / $distributionTotal) * 100, 1),
                        array_values($distributionCounts)
                    )
                    : [0, 0, 0, 0],
            ],
            'trend' => [
                'labels' => $ratedDoctors->pluck('name')->all(),
                'values' => $ratedDoctors->map(fn (Doctor $doctor) => round((float) ($doctor->avg_rating ?? 0), 1))->all(),
            ],
        ];
    }

    private function buildTrend(int $reviewsCount): array
    {
        if ($reviewsCount === 0) {
            return ['label' => 'No Data', 'direction' => 'neutral'];
        }

        return ['label' => 'New', 'direction' => 'up'];
    }

    private function statusFromAverage(float $average): array
    {
        if ($average <= 0) {
            return ['label' => 'No Reviews', 'tone' => 'neutral'];
        }

        if ($average >= 4.5) {
            return ['label' => 'Excellent', 'tone' => 'excellent'];
        }

        if ($average >= 3.0) {
            return ['label' => 'Good', 'tone' => 'good'];
        }

        return ['label' => 'Needs Improvement', 'tone' => 'low'];
    }

    private function distributionBucket(float $average, int $reviewsCount): string
    {
        if ($reviewsCount === 0 || $average <= 0) {
            return 'No Reviews';
        }

        if ($average >= 4.5) {
            return 'Excellent';
        }

        if ($average >= 3.0) {
            return 'Good';
        }

        return 'Needs Improvement';
    }

    private function colorForAverage(float $average): string
    {
        if ($average <= 0) {
            return '#9aa7b3';
        }

        if ($average >= 4.5) {
            return '#4ecb71';
        }

        if ($average >= 3.0) {
            return '#114a9f';
        }

        return '#ff5a52';
    }
}
