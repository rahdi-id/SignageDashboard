<?php

namespace App\Http\Controllers;

use App\Models\Display;
use App\Models\Event;
use App\Models\Location;
use App\Models\Promotion;
use App\Models\Schedule;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLocation = Location::count();
        $totalPromotion = Promotion::count();
        $totalEvent = Event::count();
        $totalDisplay = Display::count();
        $totalSchedule = Schedule::count();

        //Schedule
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
        return view('dashboard', ['title' => 'Dashboard', 'data' => $data, 'totalLocation' => $totalLocation, 'totalPromotion' => $totalPromotion, 'totalEvent' => $totalEvent, 'totalDisplay' => $totalDisplay, 'totalSchedule' => $totalSchedule]);
    }
}
