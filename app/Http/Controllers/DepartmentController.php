<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Support\NormalizesDepartments;

class DepartmentController extends Controller
{
    public function showDoctors(Department $department)
    {
        NormalizesDepartments::sync();

        $department->load(['doctors' => fn ($query) => $query->orderBy('name')]);
        $doctors = $department->doctors;
        return view('doctors.index', compact('department', 'doctors'));
    }

}
