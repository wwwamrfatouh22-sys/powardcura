<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Support\AuditLogger;
use App\Support\ProtectedFile;
use App\Support\TableFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffJobApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applicationsQuery = JobApplication::query()
            ->with('job:id,title,type')
            ->latest();

        TableFilters::apply($applicationsQuery, $request, [
            'date_column' => 'created_at',
            'status_column' => 'status',
        ]);

        $applicationsQuery->when(
            $request->filled('type') && $request->query('type') !== 'all',
            fn ($query) => $query->whereHas('job', fn ($jobQuery) => $jobQuery->where('type', $request->query('type')))
        );

        $applications = $applicationsQuery
            ->paginate(10)
            ->appends($request->query());

        $applications->setCollection($applications->getCollection()->map(function (JobApplication $application) {
                $cvPath = $this->resolveCvPath($application);

                $application->resolved_cv_path = $cvPath;
                $application->cv_exists = ProtectedFile::exists($cvPath);

                return $application;
            }));

        $medicalApplications = $applications
            ->filter(fn (JobApplication $application) => $application->job?->type === 'medical')
            ->values();

        $administrativeApplications = $applications
            ->filter(fn (JobApplication $application) => in_array($application->job?->type, ['administrative', 'admin'], true))
            ->values();

        return view('staff.job_applications', compact('applications', 'medicalApplications', 'administrativeApplications'));
    }

    public function approve(int $id): RedirectResponse
    {
        JobApplication::findOrFail($id)->update(['status' => 'approved']);

        return back();
    }

    public function reject(int $id): RedirectResponse
    {
        JobApplication::findOrFail($id)->update(['status' => 'rejected']);

        return back();
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        JobApplication::findOrFail($id)->update(['status' => $validated['status']]);

        return back();
    }

    public function downloadCv(int $id): StreamedResponse
    {
        $application = JobApplication::findOrFail($id);
        $cvPath = $this->resolveCvPath($application);
        AuditLogger::log('job_application.cv_downloaded', $application);

        return ProtectedFile::download($cvPath, 'job-application-' . $application->id . '.pdf');
    }

    public function viewCv(int $id): BinaryFileResponse
    {
        $application = JobApplication::findOrFail($id);
        $cvPath = $this->resolveCvPath($application);
        AuditLogger::log('job_application.cv_viewed', $application);

        return ProtectedFile::inline($cvPath, 'job-application-' . $application->id . '.pdf');
    }

    private function resolveCvPath(JobApplication $application): ?string
    {
        return ProtectedFile::normalizedPath($application->cv_path ?? $application->cv);
    }
}
