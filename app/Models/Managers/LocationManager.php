<?php

namespace App\Models\Managers;

class LocationManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'name'      => 'required',
            'latitude'  => 'required',
            'longitude' => 'required'
        ];

        return $rules;
    }
} 