<?php

namespace App\Models\Status;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];
}
