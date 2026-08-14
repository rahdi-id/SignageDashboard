<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $guarded = [];

    protected $casts = [
        'event_id' => 'integer',
        'promotion_id' => 'integer',
        'display_id' => 'integer',
    ];

    protected $appends = [
        'formatted_start_date_time',
        'formatted_end_date_time'
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function display()
    {
        return $this->belongsTo(Display::class);
    }

    public function getFormattedStartDateTimeAttribute()
    {
        return (new Carbon($this->attributes['start_date_time']))->format('H:m - d F Y');
    }

    public function getFormattedEndDateTimeAttribute()
    {
        return (new Carbon($this->attributes['end_date_time']))->format('H:m - d F Y');
    }

    public function getStartDateAttribute()
    {
        return (new Carbon($this->attributes['start_date_time']))->format('Y-m-d');
    }

    public function getEndDateAttribute()
    {
        return (new Carbon($this->attributes['end_date_time']))->format('Y-m-d');
    }

    public function getStartTimeAttribute()
    {
        return (new Carbon($this->attributes['start_date_time']))->format('H:m');
    }

    public function getEndTimeAttribute()
    {
        return (new Carbon($this->attributes['end_date_time']))->format('H:m');
    }
}
