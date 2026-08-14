<?php

namespace App\Http\Controllers;

use App\Models\Display;
use App\Models\Event;
use App\Models\Schedule;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return view('events.index', ['title' => 'Event']);
    }

    public function data()
    {
        $events = Event::all();
        $data['data'] = $events;
        return response()->json(
            $data
        );
    }

    public function create()
    {
        $displays = Display::where('status', 1)->get();
        return view('events.create', ['title' => 'Add Event', 'displays' => $displays]);
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

        $event = new Event();
        $event->name = $request->name;
        $event->participant_name = $request->participant_name;
        $event->date = $request->date;
        $event->status = $request->status;
        $event->save();

        if ($request->display) {
            $schedule = new Schedule();
            $schedule->display_id = $request->display;
            $schedule->event_id = $event->id;
            $schedule->start_date_time = date('Y-m-d H:i:s', strtotime("$request->start_date $request->start_time"));
            $schedule->end_date_time = date('Y-m-d H:i:s', strtotime("$request->end_date $request->end_time"));
            $schedule->save();
        }

        return redirect()->route('event.index')->withSuccess('Created Successfully');
    }

    public function edit($id)
    {
        $event = Event::find($id);
        $title = 'Edit Event';
        return view('events.edit', compact('event', 'title'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::find($id);
        $event->name = $request->name;
        $event->participant_name = $request->participant_name;
        $event->date = $request->date;
        $event->status = $request->status;
        $event->save();

        return redirect()->route('event.index')->withSuccess('Updated Successfully');
    }


    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();
        return redirect()->route('event.index')->withSuccess('Deleted Successfully');
    }
}
