<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesHasProcess extends Model
{
    use HasFactory;

    protected $fillable = ['role_id', 'process_id'];
}
