<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersHasProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'process_id',
        'status',
        'activity',
    ];
}
