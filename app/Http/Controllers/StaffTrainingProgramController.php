<?php

namespace App\Http\Controllers;

use App\Models\TrainingRegistration;
use App\Support\AuditLogger;
use App\Support\ProtectedFile;
use App\Support\TableFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffTrainingProgramController extends Controller
{
    public function index(Request $request): View
    {
        $registrations = TrainingRegistration::query()
            ->with(['training:id,title', 'department:id,name_en'])
            ->latest();

        TableFilters::apply($registrations, $request, [
            'date_column' => 'created_at',
            'status_column' => 'status',
        ]);

        $registrations = $registrations
            ->paginate(10)
            ->appends($request->query());

        $registrations->setCollection($registrations->getCollection()->map(function (TrainingRegistration $registration) {
                $cvPath = $this->resolveCvPath($registration->cv_path);
                $registration->resolved_cv_path = $cvPath;
                $registration->cv_exists = ProtectedFile::exists($cvPath);

                return $registration;
            }));

        return view('staff.training_programs', compact('registrations'));
    }

    public function approve(int $id): RedirectResponse
    {
        TrainingRegistration::findOrFail($id)->update([
            'status' => 'approved',
        ]);

        return back();
    }

    public function reject(int $id): RedirectResponse
    {
        TrainingRegistration::findOrFail($id)->update([
            'status' => 'rejected',
        ]);

        return back();
    }

    public function viewCv(int $id): BinaryFileResponse
    {
        $registration = TrainingRegistration::findOrFail($id);
        $cvPath = $this->resolveCvPath($registration->cv_path);
        AuditLogger::log('training_registration.cv_viewed', $registration);

        return ProtectedFile::inline($cvPath, 'training-registration-' . $registration->id . '.pdf');
    }

    public function downloadCv(int $id): StreamedResponse
    {
        $registration = TrainingRegistration::findOrFail($id);
        $cvPath = $this->resolveCvPath($registration->cv_path);
        AuditLogger::log('training_registration.cv_downloaded', $registration);

        return ProtectedFile::download($cvPath, 'training-registration-' . $registration->id . '.pdf');
    }

    private function resolveCvPath(?string $cvPath): ?string
    {
        return ProtectedFile::normalizedPath($cvPath);
    }
}
