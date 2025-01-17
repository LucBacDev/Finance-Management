<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{

    use HasFactory;
    protected $table = 'reminders';

    // Các cột có thể gán đại trà (Mass Assignment)
    protected $fillable = [
        'user_id', 
        'title', 
        'description', 
        'due_date', 
        'status'
    ];
}
