<?php

namespace App\Models\Repositories;

use Whoops\Example\Exception;
use App\Models\Entities\Configuration;
use App\Models\Entities\UserImage;
use App\Models\Entities\UserImageManager;
use App\Models\Hacks\SimpleImage;

class ConfigurationRepo extends BaseRepo {

    protected $destinationImagePath;

    public function getModel()
    {
        return new Configuration;
    }

    public function newConfig()
    {
        $config = new Configuration();
        return $config;
    }

    public function deleteConfig()
    {

    }



    public function newUserDefaultImage($userId)
    {
        $public_path = public_path();
        $userImage = new UserImage();
        $userImage->setUserId($userId);
        $userImage->setTmp(false);
        $this->destinationImagePath = $userImage->getDestinationPath();

        $origin = $public_path.'/'.$userImage->getDefaultImage();
        $destination  = $public_path.'/'.$this->destinationImagePath.'/user.jpg';

        $upload = copy($origin, $destination);
    }

    public function newUserImage($image, $userId)
    {
        $userImage = new UserImage();
        $userImage->setUserId($userId);
        $userImage->setTmp(true);
        $this->destinationImagePath = $userImage->getDestinationPath();
        $filename = $image->getClientOriginalName();
        $upload = $image->move($this->destinationImagePath, $filename);
        $this->moveUserTempImage($userId);
        return $upload;

    }

    public function moveUserTempImage($userId)
    {
        $total = 0;
        $public_path = public_path();
        $userImage = new UserImage();
        $userImage->setUserId($userId);
        $userImage->setTmp(true);

        $temp_path = $public_path.'/'.$userImage->getDestinationPath();
        $userImage->setTmp(false);
        $real_path = $public_path.'/'.$userImage->getDestinationPath();
        $move = false;

        $this->deleteUserTempImages($real_path);

        $dir = opendir($temp_path);

        while ($archivo = readdir($dir))
        {
            if($archivo != "." && $archivo != "..")
            {
                $move = copy($temp_path.'/'.$archivo , $real_path.'/'.$archivo);
                $total = $total + 1;
                $userImage->setDestinationPath($real_path.'/'.$archivo);
                $userImage->setImageName($archivo);
                $image_path = $userImage->getDestinationPath().'/'.$userImage->getImageName();
                $this->saveUserPathImage($userId, $image_path, $image_path);
            }
        }

        // if the user dont charge any image, then charge the default
        if ($total == 0)
        {
            $this->newUserDefaultImage($userId);
            //$wishImage->setDestinationPath($wishImage->getDestinationPath().$wishImage->getImageName());
            $image_path = $userImage->getDestinationPath().'/'.$userImage->getImageName();
            $this->saveUserPathImage($userId, $image_path, $image_path);
            $move = true;
        }
        else{
            // delete temp files
            $this->deleteUserTempImages($temp_path);
        }


        return $move;
    }

    public function saveUserPathImage($userId, $path, $thumb_path)
    {
        // delete previus images
        $affectedRows = UserImage::where('user_id', '=', $userId)->delete();
        // save the path in bd
        $userImage = new UserImage();
        $manager = new UserImageManager($userImage, array('path' => $path, 'thumb_path' => $thumb_path, 'user_id' => $userId));
        $manager->save();
    }

    public function deleteUserTempImages($path)
    {
        $mask = $path.'/*.*';
        array_map( "unlink", glob( $mask ) );
    }

    public function afterCreate($wish)
    {
        try{
            $this->createDirectoryTree($wish);
            $this->createWishDirectory($wish);
            // move images from temp
            $wishlist = $wish->WishList;
            $user = $wishlist->User;
            $move = $this->moveWishTempImage($user->id, $wish->id);
            // create the initial state for the Wish...
            $wishStatus = $this->newWishStatus($wish->id, 1);
            return 1;
        }
        catch (Exception $e)
        {
            return 0;
        }

    }

    public function afterCopy($wish, $wishIdOrig)
    {
        try{
            $this->createDirectoryTree($wish);
            $this->createWishDirectory($wish);
            // move images from temp
            $wishlist = $wish->WishList;
            $user = $wishlist->User;
            $move = $this->moveWishCopyImage($user->id, $wish->id, $wishIdOrig);
            // create the initial state for the Wish...
            $wishStatus = $this->newWishStatus($wish->id, 1);
            return 1;
        }
        catch (Exception $e)
        {
            return 0;
        }

    }

    public function createDirectoryTree($wish)
    {
        try{
            $public_path = public_path();
            $wishlist = $wish->WishList;
            $user = $wishlist->User;

            $base = $public_path."/assets/user/".$user->id;
            $base_img = $base.'/img';
            $base_tmp = $base.'/tmp';
            $base_wish = $base.'/img/wish';

            if (!file_exists($base))
            {
                mkdir($base, 0700);
            }

            if (!file_exists($base_img))
            {
                mkdir($base_img, 0700);
            }

            if (!file_exists($base_tmp))
            {
                mkdir($base_tmp, 0700);
            }

            if (!file_exists($base_wish))
            {
                mkdir($base_wish, 0700);
            }

            return true;
        }
        catch(Exception $e)
        {
            return true;
        }
    }

    public function createWishDirectory($wish)
    {
        try{
            $public_path = public_path();
            $wishlist = $wish->WishList;
            $user = $wishlist->User;
            $wish_dir = $public_path."/assets/user/".$user->id.'/img/wish/'.$wish->id;

            if (!file_exists($wish_dir))
            {
                mkdir($wish_dir, 0700);
            }

            return true;
        }
        catch(Exception $e)
        {
            return true;
        }
    }

    public function latestAdded()
    {
        try{
            /*$wishs = \DB::table('wish')
                ->orderBy('created_at', 'desc')
                //->groupBy('count')
                //->having('count', '>', 100)
                ->get();*/
            $wishs = Wish::all();
            return $wishs;
        }
        catch(Exception $e)
        {
            return true;
        }
    }

    public function userImageById($userId)
    {
        $userImage = UserImage::where('user_id', '=', $userId)->orderBy('id', 'desc')->first();
        return $userImage;
    }

    public function userImageCropApply($userId, $OriWidth, $OriHeight, $x, $y, $w, $h)
    {
        $userImage = $this->userImageById($userId);
        $public_path = public_path();
        $real_path = $public_path.'/'.$userImage->path;
        $image = new SimpleImage($real_path);
        // first resize image
        $image->resize($OriWidth, $OriHeight);
        $image->save($real_path);
        // then crop the image from resized one
        $image->crop($OriWidth, $OriHeight, $x, $y, $w, $h);
        $image->save($real_path);
        return true;
    }

    public function deleteFiles($path)
    {
        $mask = $path.'/*.*';
        array_map( "unlink", glob( $mask ) );
    }

}