<?php

namespace App\Http\Controllers;

use App\Models\Display;
use App\Models\Event;
use App\Models\Promotion;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $data = [];

        $schedules = Schedule::whereHas('event', function ($query) {
            return $query->where('events.status', '=', 1);
        })->orWhereHas('promotion', function ($query) {
            return $query->where('promotions.status', '=', 1);
        })->get();

        foreach ($schedules as $schedule) {
            if ($schedule->event) {
                $title = $schedule->event->name;
            } else {
                $title = $schedule->promotion->name;
            }
            $data[] = [
                'title' => $title,
                'start' => $schedule->start_date_time,
                'end' => $schedule->end_date_time,
                'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))
            ];
        }

        return view('schedules.index', ['title' => 'Schedule', 'data' => $data]);
    }

    public function data()
    {
        $schedules = Schedule::with(['display', 'event', 'promotion'])->get();
        $data['data'] = $schedules;
        return response()->json(
            $data
        );
    }

    public function create()
    {
        $events = Event::where('status', 1)->get();
        $promotions = Promotion::where('status', 1)->get();
        $displays = Display::where('status', 1)->get();
        return view('schedules.create', ['title' => 'Add Schedule', 'events' => $events, 'promotions' => $promotions, 'displays' => $displays]);
    }

    public function store(Request $request)
    {
        //Check Display Availability
        if ($request->display) {
            $displaySchedules = Schedule::where('display_id', $request->display)->get();
            foreach ($displaySchedules as $displaySchedule) {
                $eventStartDateTime =  date('Y-m-d H:i:s', strtotime("$request->start_date $request->start_time"));
                $eventEndDateTime =  date('Y-m-d H:i:s', strtotime("$request->end_date $request->end_time"));

                if (($displaySchedule->start_date_time < $eventEndDateTime) && ($displaySchedule->end_date_time > $eventStartDateTime)) {
                    return redirect()->back()->withInput()->with('error', 'Display has schedule in the inputted time');
                }
            }
        }
        $schedule = new Schedule();
        $schedule->display_id = $request->display;
        if ($request->event_promotion_type == 'Event') {
            $schedule->event_id = $request->event_promotion;
        } else {
            $schedule->promotion_id = $request->event_promotion;
        }
        $schedule->start_date_time = date('Y-m-d H:i:s', strtotime("$request->start_date $request->start_time"));
        $schedule->end_date_time = date('Y-m-d H:i:s', strtotime("$request->end_date $request->end_time"));
        $schedule->save();

        return redirect()->route('schedule.index')->withSuccess('Created Successfully');
    }

    public function edit($id)
    {
        $schedule = Schedule::find($id)->append(['start_date', 'end_date', 'start_time', 'end_time']);
        $events = Event::where('status', 1)->get();
        $promotions = Promotion::where('status', 1)->get();
        $displays = Display::where('status', 1)->get();
        $title = 'Edit Schedule';
        return view('schedules.edit', compact('schedule', 'title', 'events', 'promotions', 'displays'));
    }

    public function update(Request $request, $id)
    {
        //Check Display Availability
        if ($request->display) {
            $displaySchedules = Schedule::where('display_id', $request->display)->get();
            foreach ($displaySchedules as $displaySchedule) {
                $eventStartDateTime =  date('Y-m-d H:i:s', strtotime("$request->start_date $request->start_time"));
                $eventEndDateTime =  date('Y-m-d H:i:s', strtotime("$request->end_date $request->end_time"));

                if (($displaySchedule->start_date_time < $eventEndDateTime) && ($displaySchedule->end_date_time > $eventStartDateTime)) {
                    return redirect()->back()->withInput()->with('error', 'Display has schedule in the inputted time');
                }
            }
        }
        $schedule = Schedule::find($id);
        $schedule->display_id = $request->display;
        if ($request->event_promotion_type == 'Event') {
            $schedule->event_id = $request->event_promotion;
        } else {
            $schedule->promotion_id = $request->event_promotion;
        }
        $schedule->start_date_time = date('Y-m-d H:i:s', strtotime("$request->start_date $request->start_time"));
        $schedule->end_date_time = date('Y-m-d H:i:s', strtotime("$request->end_date $request->end_time"));
        $schedule->save();

        return redirect()->route('schedule.index')->withSuccess('Updated Successfully');
    }

    public function destroy($id)
    {
        $schedule = Schedule::find($id);
        $schedule->delete();
        return redirect()->route('schedule.index')->withSuccess('Deleted Successfully');
    }
}
