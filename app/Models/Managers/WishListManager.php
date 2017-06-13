<?php namespace Wish\Managers;

class WishListManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'name'                  => 'required'
        ];

        return $rules;
    }


} 