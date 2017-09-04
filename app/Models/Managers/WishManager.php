<?php

namespace App\Models\Managers;

class WishManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'description'   => 'required',
            'reference'     => 'required',
            'price'         => 'required',
            'list_id'       => 'required',
            //'location_id'   => 'required',
            'category_id'   => 'required'
        ];

        return $rules;
    }
} 