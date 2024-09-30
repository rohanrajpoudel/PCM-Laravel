<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    // Define the attributes that are mass assignable
    protected $fillable = [
        'name',          // The name of the task
        'description',   // A brief description of the task
        'is_completed',  // A boolean indicating whether the task is completed (true/false)
    ];
}
