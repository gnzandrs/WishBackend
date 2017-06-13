<?php namespace Wish\Managers;

class UserImageManager extends BaseManager {

    public function getRules()
    {
        $rules = [
            'path'          => 'required',
            'thumb_path'    => 'required',
            'user_id'       => 'required'
        ];

        return $rules;
    }
} 