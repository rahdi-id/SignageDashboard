<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
