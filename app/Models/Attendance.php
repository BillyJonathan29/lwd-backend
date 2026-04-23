<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    
    // Disable laravel's default timestamps since we only use attended_at
    public $timestamps = false; // Because migration doesn't have created_at updated_at. Wait, it only has attended_at

    protected $fillable = [
        'meeting_id',
        'user_id',
        'status',
        'attended_at',
        'gps_location'
    ];
    
    // Cast attended_at to datetime
    protected $casts = [
        'attended_at' => 'datetime',
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
