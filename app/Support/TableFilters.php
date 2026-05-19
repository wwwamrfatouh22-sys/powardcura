<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TableFilters
{
    public static function apply(Builder|Relation $query, Request $request, array $config = []): Builder|Relation
    {
        $dateColumn = self::resolvedColumn($query, $config['date_column'] ?? null);
        $typeColumn = self::resolvedColumn($query, $config['type_column'] ?? null);
        $statusColumn = self::resolvedColumn($query, $config['status_column'] ?? null);

        if ($typeColumn !== null) {
            $type = trim((string) $request->query('type', 'all'));

            $query->when($type !== '' && $type !== 'all', function (Builder $builder) use ($typeColumn, $type) {
                $builder->where($typeColumn, $type);
            });
        }

        if ($statusColumn !== null) {
            $status = trim((string) $request->query('status', 'all'));

            $query->when($status !== '' && $status !== 'all', function (Builder $builder) use ($statusColumn, $status) {
                $builder->where($statusColumn, $status);
            });
        }

        if ($dateColumn !== null) {
            $fromDate = self::parseDate($request->query('from_date'));
            $toDate = self::parseDate($request->query('to_date'));

            if ($fromDate || $toDate) {
                $query
                    ->when($fromDate, fn (Builder $builder) => $builder->whereDate($dateColumn, '>=', $fromDate))
                    ->when($toDate, fn (Builder $builder) => $builder->whereDate($dateColumn, '<=', $toDate));
            } else {
                $dateFilter = trim((string) $request->query('date_filter', 'all'));

                $query->when($dateFilter !== '' && $dateFilter !== 'all', function (Builder $builder) use ($dateColumn, $dateFilter) {
                    $today = now()->startOfDay();

                    match ($dateFilter) {
                        'today' => $builder->whereDate($dateColumn, $today->toDateString()),
                        'yesterday' => $builder->whereDate($dateColumn, $today->copy()->subDay()->toDateString()),
                        'last_7_days' => $builder->whereDate($dateColumn, '>=', $today->copy()->subDays(6)->toDateString()),
                        'last_30_days' => $builder->whereDate($dateColumn, '>=', $today->copy()->subDays(29)->toDateString()),
                        'this_month' => $builder
                            ->whereYear($dateColumn, $today->year)
                            ->whereMonth($dateColumn, $today->month),
                        default => null,
                    };
                });
            }
        }

        return $query;
    }

    private static function resolvedColumn(Builder|Relation $query, ?string $column): ?string
    {
        if ($column === null || $column === '') {
            return null;
        }

        $model = $query instanceof Relation ? $query->getRelated() : $query->getModel();
        $table = $model->getTable();

        return Schema::hasColumn($table, $column) ? $column : null;
    }

    private static function parseDate(mixed $value): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
