<?php

namespace App\Http\Controllers;

use App\Models\Display;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DisplayController extends Controller
{
    public function index()
    {
        return view('displays.index', ['title' => 'Display']);
    }

    public function data()
    {
        $displays = Display::with('location')->get();
        $data['data'] = $displays;
        return response()->json(
            $data
        );
    }

    public function create()
    {
        $locations = Location::all();
        return view('displays.create', ['title' => 'Add Display', 'locations' => $locations]);
    }

    public function store(Request $request)
    {
        $filename = rand(1, 9999) . time() . '.' . $request->image->getClientOriginalExtension();
        $request->image->move(public_path('images'), $filename);

        $display = new Display();
        $display->location_id = $request->location;
        $display->name = $request->name;
        $display->screen_type = $request->screen_type;
        $display->transition_time = $request->transition_time;
        $display->default_image = $filename;
        $display->status = $request->status;
        $display->code = strtoupper(bin2hex(random_bytes(3)));
        $display->save();

        return redirect()->route('display.index')->withSuccess('Created Successfully');
    }


    public function edit($id)
    {
        $display = Display::find($id);
        $locations = Location::all();
        $title = 'Edit Display';
        return view('displays.edit', compact('locations', 'display', 'title'));
    }

    public function update(Request $request, $id)
    {
        $display = Display::find($id);
        $display->location_id = $request->location;
        $display->name = $request->name;
        $display->screen_type = $request->screen_type;
        $display->transition_time = $request->transition_time;
        $display->status = $request->status;

        if ($request->image) {
            File::delete('images/' . $display->default_image);
            $filename = rand(1, 9999) . time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $filename);
            $display->default_image = $filename;
        }

        $display->save();

        return redirect()->route('display.index')->withSuccess('Updated Successfully');
    }

    public function destroy($id)
    {
        $display = Display::find($id);
        File::delete('images/' . $display->default_image);
        $display->delete();
        return redirect()->route('display.index')->withSuccess('Deleted Successfully');
    }
}
