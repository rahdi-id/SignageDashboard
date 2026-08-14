<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return view('locations.index', ['title' => 'Location']);
    }

    public function data()
    {
        $locations = Location::all();
        $data['data'] = $locations;
        return response()->json(
            $data
        );
    }

    public function create()
    {
        return view('locations.create', ['title' => 'Add Location']);
    }

    public function store(Request $request)
    {
        $location = new Location();
        $location->name = $request->name;
        $location->category = $request->category;
        $location->floor = $request->floor;
        $location->status = $request->status;
        $location->description = $request->description;
        $location->save();

        return redirect()->route('location.index')->withSuccess('Created Successfully');
    }


    public function edit($id)
    {
        $location = Location::find($id);
        $title = 'Edit Location';
        return view('locations.edit', compact('location', 'title'));
    }

    public function update(Request $request, $id)
    {
        $location = Location::find($id);

        $location->name = $request->name;
        $location->category = $request->category;
        $location->floor = $request->floor;
        $location->status = $request->status;
        $location->description = $request->description;
        $location->save();

        return redirect()->route('location.index')->withSuccess('Updated Successfully');
    }

    public function destroy($id)
    {
        $location = Location::find($id);
        $location->delete();
        return redirect()->route('location.index')->withSuccess('Deleted Successfully');
    }
}
