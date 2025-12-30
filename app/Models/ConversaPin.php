<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversaPin extends Model
{
    protected $table = 'conversa_pins';
    protected $fillable = ['user_id', 'conversa_id'];
}
