<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersHasProcess extends Model
{
    use HasFactory;

    /* Telling Laravel to use the pgsql connection instead of the default mysql connection. */
    protected $connection = 'pgsql';

    protected $fillable = [
        'user_id',
        'process_id',
        'status',
        'activity',
    ];
}
