<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Meeting extends Model
{
    use HasFactory, HasApiTokens,  Notifiable;


    protected $fillable = [
        'topic_title',
        'description',
        'date',
        'category',
        'assignment_deadline'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
