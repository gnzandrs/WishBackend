<?php namespace Wish\Managers;

class WishStatusManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'wish_id'                  => 'required',
            'status'                    => 'required'
        ];

        return $rules;
    }


} 