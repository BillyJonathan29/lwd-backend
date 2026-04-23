<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Submission extends Model
{
    use HasFactory, HasApiTokens,  Notifiable;

    protected $fillable = [
        'meeting_id',
        'user_id',
        'file_or_link',
        'member_notes',
        'grade',
        'admin_feedback'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
