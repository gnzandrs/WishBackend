<?php namespace Wish\Managers;

class LocationManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'latitude'   => 'required',
            'longitude'     => 'required'
        ];

        return $rules;
    }


} 