<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Display;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->input('code');
        $display = Display::with(['location', 'design'])->where('code', $code)->first();

        //tambahan baru diubah
        if ($display && $display->default_image) {
            $display->image_url = url('/api/image/' . $display->default_image);
        }

        if ($display && $display->design) {
            if ($display->design->main_image) {
                $display->design->main_image_url = url(
                    '/api/image/' . $display->design->main_image
                );
            }

            if ($display->design->header_side_image) {
                $display->design->header_side_image_url = url(
                    '/api/image/' . $display->design->header_side_image
                );
            }

            if ($display->design->hotel_logo) {
                $display->design->hotel_logo_url = url(
                    '/api/image/' . $display->design->hotel_logo
                );
            }
        }
        //sampai sini

        $schedule = Schedule::with(['event', 'promotion.medias'])->where(
            function ($query) {
                return $query
                    ->whereHas('event', function ($query) {
                        return $query->where('events.status', '=', 1);
                    })->orWhereHas('promotion', function ($query) {
                        return $query->where('promotions.status', '=', 1);
                    });
            }
        )->where([['start_date_time', '<=', Carbon::now()], ['end_date_time', '>', Carbon::now()]])->where('display_id', $display->id)->first();


        if ($schedule) {
            return  [
                'status' => true,
                'message' => 'Success get Event/Promotion',
                'data' => [
                    'schedule' => $schedule,
                    'display' => $display
                ]
            ];
        } else {
            return [
                'status' => true,
                'message' => 'No Event/Promotion',
                'data' => [
                    'display' => $display
                ]
            ];
        }
    }
}
