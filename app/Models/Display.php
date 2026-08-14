<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Display extends Model
{
    use HasFactory;

    protected $table = 'displays';
    protected $guarded = [];
    
    protected $casts = [
        'location_id' => 'integer',
        'transition_time' => 'integer',
        'status' => 'integer',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function design(){
        return $this->hasOne(Design::class)->latest();
    }
}
