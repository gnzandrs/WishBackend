<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class UserImage extends Model
{
    protected $table = 'user_image';
    protected $fillable = ['path','thumb_path','user_id'];
    protected $tmp = false;
    protected $destinationPath;
    protected $imageName = "user.jpg";
    protected $imageThumbName = "tb_user.jpg";
    protected $userId;

    public function User()
    {
        return $this->belongsTo('App\Models\Entities\User', 'user_id', 'id');
    }

    public function setTmp($val)
    {
        $this->tmp = $val;
    }

    public function getTmp()
    {
        return $this->tmp;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function setImageThumbName($imageThumbName)
    {
        $this->imageThumbName = $imageThumbName;
    }

    public function getImageThumbName()
    {
        return $this->imageThumbName;
    }

    public function setDestinationPath($destinationPath)
    {
        $this->destinationPath = $destinationPath;
    }

    public function getDestinationPath()
    {
        if($this->tmp)
        {
            $this->createDestinationTmpPath();
        }
        else{
            $this->createDestinationPath();
        }
        return $this->destinationPath;
    }

    public function createDestinationPath()
    {
        $user = $this->userId;
        $user_path = "assets/user/$user/img/profile";
        $this->setDestinationPath($user_path);
    }

    public function createDestinationTmpPath()
    {
        $user = $this->userId;
        $user_path = "assets/user/$user/tmp";
        $this->setDestinationPath($user_path);
    }

    public function getDefaultImage()
    {
        return "img/default/user.jpg";
    }
}
