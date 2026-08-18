<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    protected $guarded = [];

    protected $casts = [
        'conversation_id' => 'integer',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
