<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotions';
    protected $guarded = [];
    protected $appends = ['formatted_date'];
    
    protected $casts = [
        'status' => 'integer',
    ];

    public function getFormattedDateAttribute()
    {
        return (new Carbon($this->attributes['date']))->format('d F Y');
    }

    public function medias(){
        return $this->hasMany(PromotionMedia::class);
    }
}
