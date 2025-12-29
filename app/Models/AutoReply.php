<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoReply extends Model
{
    protected $fillable = [
        'trigger',
        'response',
        'active',
        'priority',
    ];
    protected $casts = [
        'active' => 'boolean',
        'priority' => 'integer',
    ];
}
