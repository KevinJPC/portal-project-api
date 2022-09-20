<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;

    protected $fillable = ['se_oid', 'se_name', 'name', 'visible', 'state'];
}
