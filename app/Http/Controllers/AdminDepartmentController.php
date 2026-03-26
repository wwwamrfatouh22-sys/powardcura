<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class AdminDepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['doctor'])->withCount('doctors')->get();
        return view('admin.departments', compact('departments'));
    }
    public function create()
    {
        $doctors = \App\Models\Doctor::pluck('name', 'id');
        return view('admin.create_department', compact('doctors'));
    }

    public function store(Request $request)
    {
        Department::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'head_name' => $request->head_name,
        ]);

        return redirect()->route('admin.departments')->with('success', 'Department Added Successfully');
    }
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('admin.edit_department', compact('department'));
    }
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $department->update([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'head_name' => $request->head_name,
            'status'    => $request->status,

        ]);

        return redirect()->route('admin.departments')
            ->with('success', 'Department Updated Successfully');
    }

    public function destroy($id)
    {
        Department::findOrFail($id)->delete();

        return redirect()->route('admin.departments')
            ->with('success', 'Department Deleted Successfully');
    }

}
