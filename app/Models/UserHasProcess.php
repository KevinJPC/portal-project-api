<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'process_id',
        'status',
        'activity',
    ];
}
