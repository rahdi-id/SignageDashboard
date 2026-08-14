<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Display;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DesignController extends Controller
{
    public function index($id)
    {
        $display = Display::find($id);
        return view('designs.index', ['title' => 'Design', 'display' => $display]);
    }

    public function data($id)
    {
        $designs = Design::where('display_id', $id)->get();
        $data['data'] = $designs;
        return response()->json(
            $data
        );
    }

    public function create($id)
    {
        $display = Display::find($id);
        return view('designs.create', ['title' => 'Add Custom Design', 'display' => $display]);
    }


    public function store(Request $request, $id)
    {
        $headerSideImageName = rand(1, 9999) . time() . '.' . $request->header_side_image->getClientOriginalExtension();
        $mainImageName = rand(1, 9999) . time() . '.' . $request->main_image->getClientOriginalExtension();
        $request->header_side_image->move(public_path('images'), $headerSideImageName);
        $request->main_image->move(public_path('images'), $mainImageName);

        $design = new Design();
        $design->display_id = $id;
        $design->hotel_logo = $request->hotel_logo;
        $design->header_side_image = $headerSideImageName;
        $design->main_image = $mainImageName;
        $design->font_color_header_side = $request->font_color_header_side;
        $design->font_color_main = $request->font_color_main;
        $design->opacity = $request->opacity;
        $design->status = $request->status;
        $design->save();

        return redirect()->route('design.index', $id)->withSuccess('Created Successfully');
    }

    public function edit($id, $designId)
    {
        $design = Design::find($designId);
        $display = Display::find($id);
        $title = 'Edit Design';
        return view('designs.edit', compact('design', 'title', 'display'));
    }

    public function update(Request $request, $id, $designId)
    {
        $design = Design::find($designId);
        $design->hotel_logo = $request->hotel_logo;
        $design->font_color_header_side = $request->font_color_header_side;
        $design->font_color_main = $request->font_color_main;
        $design->opacity = $request->opacity;
        $design->status = $request->status;

        if ($request->header_side_image) {
            File::delete('images/' . $design->header_side_image);
            $filename = rand(1, 9999) . time() . '.' . $request->header_side_image->getClientOriginalExtension();
            $request->header_side_image->move(public_path('images'), $filename);
            $design->header_side_image = $filename;
        }

        if ($request->main_image) {
            File::delete('images/' . $design->main_image);
            $filename = rand(1, 9999) . time() . '.' . $request->main_image->getClientOriginalExtension();
            $request->main_image->move(public_path('images'), $filename);
            $design->main_image = $filename;
        }

        $design->save();

        return redirect()->route('design.index', $id)->withSuccess('Updated Successfully');
    }

    public function destroy($id, $designId)
    {
        $design = Design::find($designId);
        File::delete('images/' . $design->main_image);
        File::delete('images/' . $design->header_side_image);
        $design->delete();
        return redirect()->route('design.index', $id)->withSuccess('Deleted Successfully');
    }
}
