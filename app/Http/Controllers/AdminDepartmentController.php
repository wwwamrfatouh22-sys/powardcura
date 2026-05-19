<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Support\DeletionGuard;
use App\Support\DepartmentCatalog;
use App\Support\NormalizesDepartments;
use App\Support\TableFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdminDepartmentController extends Controller
{
    public function index(Request $request)
    {
        NormalizesDepartments::sync();

        $staffCountResolver = static function (Department $department): int {
            if (Schema::hasTable('staff') && Schema::hasColumn('staff', 'department_id')) {
                return (int) \DB::table('staff')->where('department_id', $department->id)->count();
            }

            return 0;
        };

        $departments = Department::query()->with([
                'doctor',
                'doctors' => fn ($query) => $query->orderBy('name'),
            ])
            ->withCount('doctors')
            ->orderBy('name_en');

        TableFilters::apply($departments, $request, [
            'date_column' => 'created_at',
            'status_column' => 'status',
        ]);

        $filteredDepartmentsCount = (clone $departments)->count();
        $departments = $departments
            ->paginate(10)
            ->appends($request->query());

        Log::debug('Admin departments index query results.', [
            'filters' => $request->query(),
            'filtered_count' => $filteredDepartmentsCount,
            'page_count' => $departments->count(),
            'total_departments' => Department::query()->count(),
            'total_doctors' => \App\Models\Doctor::query()->count(),
        ]);

        $departments->setCollection($departments->getCollection()->each(function (Department $department) use ($staffCountResolver) {
                $department->setAttribute('staff_count', $staffCountResolver($department));
            }));

        return view('admin.departments', compact('departments'));
    }
    public function create()
    {
        NormalizesDepartments::sync();

        $departmentOptions = DepartmentCatalog::selectable();
        return view('admin.create_department', compact('departmentOptions'));
    }

    public function store(Request $request)
    {
        NormalizesDepartments::sync();

        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'head_name' => 'nullable|string|max:255',
            'doctor_id' => 'nullable|exists:doctors,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        $pair = DepartmentCatalog::pairFromInput($validated['name_en']);

        if ($pair === null) {
            return back()->withErrors(['name_en' => 'Select a valid department from the required list.'])->withInput();
        }

        $existingDepartment = Department::query()
            ->where('name_en', $pair['name_en'])
            ->orWhere('name_ar', $pair['name_ar'])
            ->first();

        if ($existingDepartment) {
            $existingDepartment->update([
                'name_en' => $pair['name_en'],
                'name_ar' => $pair['name_ar'],
                'head_name' => $validated['head_name'] ?? $existingDepartment->head_name,
                'doctor_id' => $validated['doctor_id'] ?? $existingDepartment->doctor_id,
                'status' => $validated['status'] ?? $existingDepartment->status ?? 'active',
            ]);

            return redirect()->route('admin.departments')->with('success', 'Department already existed, so no duplicate was added.');
        }

        Department::create([
            'name_en' => $pair['name_en'],
            'name_ar' => $pair['name_ar'],
            'head_name' => $validated['head_name'] ?? null,
            'doctor_id' => $validated['doctor_id'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return redirect()->route('admin.departments')->with('success', 'Department Added Successfully');
    }
    public function edit($id)
    {
        NormalizesDepartments::sync();

        $department = Department::findOrFail($id);
        $departmentOptions = DepartmentCatalog::selectable();
        return view('admin.edit_department', compact('department', 'departmentOptions'));
    }
    public function update(Request $request, $id)
    {
        NormalizesDepartments::sync();

        $department = Department::findOrFail($id);
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'head_name' => 'nullable|string|max:255',
            'doctor_id' => 'nullable|exists:doctors,id',
            'status' => 'required|in:active,inactive',
        ]);

        $pair = DepartmentCatalog::pairFromInput($validated['name_en']);

        if ($pair === null) {
            return back()->withErrors(['name_en' => 'Select a valid department from the required list.'])->withInput();
        }

        $duplicateExists = Department::query()
            ->where(function ($query) use ($pair) {
                $query->where('name_en', $pair['name_en'])
                    ->orWhere('name_ar', $pair['name_ar']);
            })
            ->where('id', '!=', $department->id)
            ->exists();

        if ($duplicateExists) {
            return back()->withErrors(['name_en' => 'That department already exists in English.'])->withInput();
        }

        $department->update([
            'name_en' => $pair['name_en'],
            'name_ar' => $pair['name_ar'],
            'head_name' => $validated['head_name'] ?? null,
            'doctor_id' => $validated['doctor_id'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.departments')
            ->with('success', 'Department Updated Successfully');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        DeletionGuard::deleteOne($department, 'department.deleted', ['source' => 'admin']);

        return redirect()->route('admin.departments')
            ->with('success', 'Department Deleted Successfully');
    }

}
