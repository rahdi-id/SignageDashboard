<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index()
    {
        return view('admins.index', ['title' => 'Administrator']);
    }

    public function data()
    {
        $users = User::all();
        $data['data'] = $users;
        return response()->json(
            $data
        );
    }

    public function create()
    {
        return view('admins.create', ['title' => 'Add Admin']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', Password::min(8)],
            'email' => ['unique:users,email']
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.index')->withSuccess('Created Successfully');
    }

    public function edit($id)
    {
        $admin = User::find($id);
        $title = 'Edit Admin';
        return view('admins.edit', compact('title', 'admin'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', Password::min(8)],
            'email' => ['unique:users,email']
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.index')->withSuccess('Updated Successfully');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('admin.index')->withSuccess('Deleted Successfully');
    }
}
