<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionMedia extends Model
{
    use HasFactory;
    
    protected $table = 'promotion_medias';
    protected $guarded = [];

    
    protected $casts = [
        'promotion_id' => 'integer',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

}
