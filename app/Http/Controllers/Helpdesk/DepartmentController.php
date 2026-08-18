<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('helpdesk.departments.index', ['title' => 'Departments']);
    }

    public function data()
    {
        $departments = Department::withCount('conversations')->get();
        return response()->json(['data' => $departments]);
    }

    public function create()
    {
        return view('helpdesk.departments.create', ['title' => 'Add Department']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:departments,name',
            'description' => 'nullable|string|max:255',
        ]);

        Department::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'is_active'   => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('helpdesk.departments.index')
            ->withSuccess('Department created successfully.');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        return view('helpdesk.departments.edit', [
            'title'      => 'Edit Department',
            'department' => $department,
        ]);
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:100|unique:departments,name,' . $id,
            'description' => 'nullable|string|max:255',
        ]);

        $department->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'is_active'   => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('helpdesk.departments.index')
            ->withSuccess('Department updated successfully.');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()->route('helpdesk.departments.index')
            ->withSuccess('Department deleted successfully.');
    }
}
