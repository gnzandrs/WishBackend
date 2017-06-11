<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'log';
    protected $fillable = ['file','class','description', 'exception'];
}
