<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobFailures extends Model
{
    protected $fillable = [
        'job_name',
        'error',
        'identifier',
        'identifier_type'
    ];
}
