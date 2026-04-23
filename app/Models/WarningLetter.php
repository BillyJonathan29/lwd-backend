<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class WarningLetter extends Model
{
    use HasFactory, HasApiTokens,  Notifiable;
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'admin_id',
        'warning_level',
        'reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
