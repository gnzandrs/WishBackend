<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public function __construct()
    {
        $this->model = $this->getModel();
    }
}
