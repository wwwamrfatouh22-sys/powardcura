<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('departments', compact('departments'));
    }

    public function show(Department $department)
    {
        $doctors = $department->doctors;
        return view('doctors.index', compact('department', 'doctors'));
    }

}
