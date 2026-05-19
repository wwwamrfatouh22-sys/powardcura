<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Staff;
use App\Support\AuditLogger;
use App\Support\DeletionGuard;
use App\Support\NormalizesDepartments;
use App\Support\TableFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminStaffController extends Controller
{
    public function index(Request $request)
    {
        NormalizesDepartments::sync();

        $staff = Staff::query()
            ->with('department')
            ->latest();

        TableFilters::apply($staff, $request, [
            'date_column' => 'created_at',
            'type_column' => 'role',
            'status_column' => 'status',
        ]);

        $search = trim((string) $request->query('q', ''));

        $staff->when($search !== '', function ($query) use ($search): void {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('full_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });

        $staff = $staff->paginate(10)->appends($request->query());

        return view('admin.staff.index', [
            'staff' => $staff,
            'roles' => Staff::ROLES,
            'statuses' => Staff::STATUSES,
        ]);
    }

    public function create()
    {
        NormalizesDepartments::sync();

        return view('admin.staff.create', [
            'departments' => Department::query()->orderBy('name_en')->get(),
            'roles' => Staff::ROLES,
            'statuses' => Staff::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        NormalizesDepartments::sync();

        $validated = $request->validate($this->rules());
        $fullName = trim($validated['full_name']);

        $staff = Staff::query()->create([
            'full_name' => $fullName,
            'name' => $fullName,
            'email' => trim($validated['email']),
            'phone' => isset($validated['phone']) ? trim((string) $validated['phone']) : null,
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        AuditLogger::log('staff.created', $staff, ['source' => 'admin']);

        return redirect()
            ->route('admin.staff')
            ->with('success', "Staff member {$staff->displayName()} added successfully.");
    }

    public function edit(int $id)
    {
        NormalizesDepartments::sync();

        return view('admin.staff.edit', [
            'staffMember' => Staff::query()->findOrFail($id),
            'departments' => Department::query()->orderBy('name_en')->get(),
            'roles' => Staff::ROLES,
            'statuses' => Staff::STATUSES,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        NormalizesDepartments::sync();

        $staff = Staff::query()->findOrFail($id);
        $validated = $request->validate($this->rules($staff));
        $fullName = trim($validated['full_name']);

        $payload = [
            'full_name' => $fullName,
            'name' => $fullName,
            'email' => trim($validated['email']),
            'phone' => isset($validated['phone']) ? trim((string) $validated['phone']) : null,
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $staff->update($payload);
        AuditLogger::log('staff.updated', $staff, ['source' => 'admin']);

        return redirect()->route('admin.staff')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $staff = Staff::query()->findOrFail($id);
        DeletionGuard::deleteOne($staff, 'staff.deleted', ['source' => 'admin']);

        return redirect()->route('admin.staff')->with('success', 'Staff member deleted safely.');
    }

    private function rules(?Staff $staff = null): array
    {
        $emailRule = Rule::unique('staff', 'email');

        if ($staff) {
            $emailRule->ignore($staff->id);
        }

        $passwordRule = $staff
            ? ['nullable', 'confirmed', 'min:8']
            : ['required', 'confirmed', 'min:8'];

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', Rule::in(array_keys(Staff::ROLES))],
            'department_id' => ['nullable', 'exists:departments,id'],
            'password' => $passwordRule,
            'status' => ['required', Rule::in(array_keys(Staff::STATUSES))],
        ];
    }
}
