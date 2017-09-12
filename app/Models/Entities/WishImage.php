<?php

namespace App\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class WishImage extends Model
{
    protected $table = 'wish_image';
    protected $fillable = ['path','thumb_path','wish_id'];
    protected $tmp = false;
    protected $destinationPath;
    protected $imageName = "wish.jpg";
    protected $imageThumbName = "tb_wish.jpg";
    protected $userId;
    protected $wishId;
    protected $wishListId;

    public function __construct()
    {

    }

    public function wish()
    {
        return $this->belongsTo('App\Models\Entities\Wish');
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

    public function setWishId($wishId)
    {
        $this->wishId = $wishId;
    }

    public function getWishId()
    {
        return $this->wishId;
    }

    public function setWishListId($wishListId)
    {
        $this->wishListId = $wishListId;
    }

    public function getWishListId()
    {
        return $this->wishListId;
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
      else {
          $this->createDestinationPath();
      }
      return $this->destinationPath;
    }

    public function createDestinationPath()
    {
        $user = $this->userId;
        $id = $this->wishId;
        $user_path = "assets/user/$user/img/wish/$id";
        $this->setDestinationPath($user_path);
    }

    public function createDestinationTmpPath()
    {
        $user = $this->userId;
        $userPath = "assets/user/$user/tmp/wishlist/$this->wishListId/$this->wishId";
        $this->setDestinationPath($userPath);
    }

    public function getDefaultImage()
    {
        return "img/default/wish.jpg";
    }
}
