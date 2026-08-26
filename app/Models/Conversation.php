<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';
    protected $guarded = [];

    protected $casts = [
        'department_id'       => 'integer',
        'guest_last_seen_at'  => 'datetime',
        'admin_last_seen_at'  => 'datetime',
    ];

    /**
     * Status label mapping.
     */
    public const STATUS_LABELS = [
        'open'        => 'Open',
        'in_progress' => 'In Progress',
        'closed'      => 'Closed',
    ];

    /**
     * Priority label mapping.
     */
    public const PRIORITY_LABELS = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
    ];

    /**
     * Status badge color (Bootstrap 4).
     */
    public const STATUS_COLORS = [
        'open'        => 'primary',
        'in_progress' => 'warning',
        'closed'      => 'success',
    ];

    /**
     * Priority badge color (Bootstrap 4).
     */
    public const PRIORITY_COLORS = [
        'low'    => 'secondary',
        'medium' => 'info',
        'high'   => 'danger',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }
}
