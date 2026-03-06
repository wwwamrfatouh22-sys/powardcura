<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class MedicalDepartmentsController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('medicaldepartments', compact('departments'));
    }
    public function show(Department $department)
    {
        $doctors = $department->doctors;
        return view('doctors.index', compact('department', 'doctors'));
    }
}
