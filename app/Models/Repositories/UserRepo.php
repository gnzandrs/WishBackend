<?php namespace App\Models\Repositories;

use App\Models\Entities\User;
use App\Models\Entities\UserImage;
use Illuminate\Support\Facades\BD;

class UserRepo extends BaseRepo {

    public function getModel()
    {
        return new User;
    }

    public function newUser()
    {
        $user = new User();
        $user->type = 'user';
        return $user;
    }

    /**
     * Create a basic directory structure to storage
     * the user's assets.
     * @param  \App\Models\Entities\User
     * @return bool $result
     */
    public function createDirectoryTree($user)
    {
        try{
            $publicPath = public_path();

            $base = $publicPath."/assets/user/".$user->id;
            $baseImg = $base.'/img';
            $baseTmp = $base.'/tmp';
            $baseWish = $base.'/img/wish';
            $baseProfile = $base.'/img/profile';

            if (!file_exists($base))
            {
                mkdir($base, 0700);
            }

            if (!file_exists($baseImg))
            {
                mkdir($baseImg, 0700);
            }

            if (!file_exists($baseTmp))
            {
                mkdir($baseTmp, 0700);
            }

            if (!file_exists($baseWish))
            {
                mkdir($baseWish, 0700);
            }

            if (!file_exists($baseProfile))
            {
                mkdir($baseProfile, 0700);
            }

            return true;
        }
        catch(Exception $e)
        {
            return true;
        }
    }

    public function getConfig($userId)
    {
        $user = User::find($userId);
        $config = $user->configuration;
        return $config;
    }

    public function newAdmin()
    {
        $user = new User();
        $user->type = 'admin';
        return $user;
    }

    public function wishLists($userId)
    {
        $wishLists = \DB::table('list')->where('user_id', $userId)->get();
        return $wishLists;
    }

    public function userCheck($userName)
    {
        $available = \DB::table('user')->where('username', $userName)->get();
        if(count($available) == 0)
        {
            return 1;
        }
        else{
            return 0;
        }
    }

    public function emailCheck($email)
    {
        $available = \DB::table('user')->where('email', $email)->get();
        if(count($available) == 0)
        {
            return 1;
        }
        else{
            return 0;
        }
    }

    public function getAvatarImage($userId)
    {
        $total = 0;
        $public_path = public_path();
        $userImage = new UserImage();
        $userImage->setUserId($userId);
        $userImage->setTmp(false);
        $real_path = $public_path.'/'.$userImage->getDestinationPath();

        $dir = opendir($real_path);
        $image_path = "";

        while ($archivo = readdir($dir))
        {
            if($archivo != "." && $archivo != "..")
            {
                //$image_path = $userImage->getDestinationPath().'/'.$archivo;
                $image_path = $archivo;
            }
        }

        return $image_path;

    }

}
