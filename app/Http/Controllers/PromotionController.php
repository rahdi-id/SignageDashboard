<?php

namespace App\Http\Controllers;

use App\Models\Display;
use App\Models\Promotion;
use App\Models\Schedule;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        return view('promotions.index', ['title' => 'Promotion']);
    }

    public function data()
    {
        $promotions = Promotion::all();
        $data['data'] = $promotions;
        return response()->json(
            $data
        );
    }

    public function create()
    {
        $displays = Display::where('status', 1)->get();
        return view('promotions.create', ['title' => 'Add Promotion', 'displays' => $displays]);
    }

    public function store(Request $request)
    {
        //Check Display Availability
        if ($request->display) {
            $displaySchedules = Schedule::where('display_id', $request->display)->get();
            foreach ($displaySchedules as $displaySchedule) {
                $promotionStartDateTime =  date('Y-m-d H:i:s', strtotime("$request->start_date $request->start_time"));
                $promotionEndDateTime =  date('Y-m-d H:i:s', strtotime("$request->end_date $request->end_time"));

                if (($displaySchedule->start_date_time < $promotionEndDateTime) && ($displaySchedule->end_date_time > $promotionStartDateTime)) {
                    return redirect()->back()->withInput()->with('error', 'Display has schedule in the inputted time');
                }
            }
        }

        $promotion = new Promotion();
        $promotion->name = $request->name;
        $promotion->screen_type = $request->screen_type;
        $promotion->date = $request->date;
        $promotion->status = $request->status;
        $promotion->save();

        if ($request->display) {
            $schedule = new Schedule();
            $schedule->display_id = $request->display;
            $schedule->promotion_id = $promotion->id;
            $schedule->start_date_time = date('Y-m-d H:i:s', strtotime("$request->start_date $request->start_time"));
            $schedule->end_date_time = date('Y-m-d H:i:s', strtotime("$request->end_date $request->end_time"));
            $schedule->save();
        }


        return redirect()->route('promotion.index')->withSuccess('Created Successfully');
    }


    public function edit($id)
    {
        $promotion = Promotion::find($id);
        $title = 'Edit Promotion';
        return view('promotions.edit', compact('promotion', 'title'));
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::find($id);
        $promotion->name = $request->name;
        $promotion->screen_type = $request->screen_type;
        $promotion->date = $request->date;
        $promotion->status = $request->status;
        $promotion->save();

        return redirect()->route('promotion.index')->withSuccess('Updated Successfully');
    }


    public function destroy($id)
    {
        $promotion = Promotion::find($id);
        $promotion->delete();
        return redirect()->route('promotion.index')->withSuccess('Deleted Successfully');
    }
}
