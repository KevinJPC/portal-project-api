<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolHasProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'rol_id',
        'process_id',
    ];
}
