<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ActionLog stores every action performed in the application.
 *
 * Pedagogical goals:
 * - Introduce security logging (ANSSI good practices)
 * - Allow detection of suspicious patterns later
 * - Support exercises: purge logs, filter logs, improve logging
 */
class ActionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'idea_id',
        'comment_id',
        'data_before',
        'data_after',
        'ip_address',
        'user_agent',
        'session_id',
        'severity',
        'result',
        'category',
        'http_status',
        'checksum',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
