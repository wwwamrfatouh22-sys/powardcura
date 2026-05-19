<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingRegistrationRequest;
use App\Models\Department;
use App\Models\TrainingProgram;
use App\Models\TrainingRegistration;
use App\Support\AuditLogger;
use App\Support\ProtectedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrainingProgramController extends Controller
{
    public function index(): View
    {
        $programs = TrainingProgram::query()
            ->where('is_active', true)
            ->with('department:id,name_en')
            ->orderBy('title')
            ->get();

        return view('training.index', compact('programs'));
    }

    public function register(TrainingProgram $program): View
    {
        $departments = Department::orderBy('name_en')->get(['id', 'name_en']);

        return view('training.register', compact('program', 'departments'));
    }

    public function storeApplication(TrainingRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        abort_unless(TrainingProgram::query()->whereKey($validated['training_id'])->where('is_active', true)->exists(), 404);

        $cvPath = ProtectedFile::storeTrainingCv($request->file('cv'));

        $registration = TrainingRegistration::create([
            'training_id' => $validated['training_id'],
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'national_id' => $validated['national_id'],
            'department_id' => $validated['department_id'] ?? null,
            'age' => $validated['age'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'university' => $validated['university'] ?? null,
            'gpa' => $validated['gpa'] ?? null,
            'cv_path' => $cvPath,
            'status' => 'pending',
        ]);
        AuditLogger::log('training_registration.created', $registration);

        return redirect()
            ->route('training.index')
            ->with('success', 'Training registration submitted successfully.');
    }
}
