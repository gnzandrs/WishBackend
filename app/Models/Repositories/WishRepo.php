<?php namespace App\Models\Repositories;

use App\Models\Entities\Wish;
use App\Models\Entities\WishImage;
use App\Models\Entities\WishStatus;
use App\Models\Entities\Category;
use App\Models\Managers\ImageManager;
use App\Models\Managers\WishStatusManager;
use App\Models\Hacks\SimpleImage;

class WishRepo extends BaseRepo {


    protected $destinationImagePath;

    public function getModel()
    {
        return new Wish;
    }

    public function newWish()
    {
        $wish = new Wish();
        $wishStatus = new WishStatus($wish->id, 0);
        return $wish;
    }

    public function deleteImageFromDb($wishId, $imageName)
    {
        try {
            //$image = WishImage::where('wish_id', '=', $wishId)->('path', 'like', '%'.$imageName)->delete();
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    public function deleteImageFromTemp($userId, $imageName)
    {
        try {
            $public_path = public_path();
            $wishImage = new WishImage();
            $wishImage->setUserId($userId);
            //$wishImage->setWishId($wishId);
            $wishImage->setTmp(true);

            $temp_path = $public_path.'/'.$wishImage->getDestinationPath();

            $dir = opendir($temp_path);

            while ($archivo = readdir($dir))
            {
                if($archivo != "." && $archivo != "..")
                {
                    if ($archivo == $imageName)
                    {
                        unlink($temp_path . '/' .$archivo);
                        //unlink($temp_path . '/' .'tb_' . $archivo);
                    }
                }
            }

            return 1;
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    public function deleteImageFromWish($userId, $imageName, $wishId, $imgId)
    {
        try {
            $public_path = public_path();
            $wishImage = new WishImage();
            $wishImage->setUserId($userId);
            $wishImage->setWishId($wishId);
            $wishImage->setTmp(false);

            $real_path = $public_path.'/'.$wishImage->getDestinationPath();

            $dir = opendir($real_path);

            // delete from directory
            while ($archivo = readdir($dir))
            {
                if($archivo != "." && $archivo != "..")
                {
                    if ($archivo == $imageName)
                    {
                        unlink($real_path . '/' .$archivo);
                        unlink($real_path . '/' .'tb_' . $archivo);
                    }
                }
            }

            // delete from db
            WishImage::find($imgId)->delete();
            return 1;
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    public function deleteWish($wish)
    {
        try{
            $wish_images = $wish->WishImages;
            foreach ($wish_images as $image)
            {
                $image->delete();
            }

            \DB::table('wish_status')->where('wish_id', '=', $wish->id)->delete();

            $wish->delete();
            return 1;
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    public function deleteWishTempImages($path)
    {
        $mask = $path.'/*.*';
        array_map( "unlink", glob( $mask ) );
    }

    public function deleteWishUserTempImages($userId)
    {
        try {
            $public_path = public_path();
            $wishImage = new WishImage();
            $wishImage->setUserId($userId);
            $wishImage->setTmp(true);

            $temp_path = $public_path.'/'.$wishImage->getDestinationPath();

            $mask = $temp_path.'/*.*';
            array_map( "unlink", glob( $mask ) );

            return 1;
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    public function getImages($wishId)
    {
        $wishImage = WishImage::where('wish_id', '=', $wishId)->get();
        $result  = array();

        foreach ( $wishImage as $image ) {
            $obj['img_id'] = $image->id;
            $obj['img'] = asset($image->path);
            $obj['img_thumb'] = asset($image->thumb_path);

            $search = strripos($image->path, '/');
            $length = strlen($image->path);
            $name = substr($image->path, $search + 1, $length);

            $obj['img_name'] = $name;

            $path = public_path() .'/'. $image->path;
            $size = filesize($path);

            $obj['img_size'] = $size;
            $result[] = $obj;
        }

        return $result;
    }

    // try to get the image from a user by id
    public function getImageByName($user)
    {
        $wishImage = WishImage::where('wish_id', '=', $wishId)->get();
        $result  = array();

        foreach ( $wishImage as $image ) {
            $obj['img'] = asset($image->path);
            $obj['img_thumb'] = asset($image->thumb_path);

            $search = strripos($image->path, '/');
            $length = strlen($image->path);
            $name = substr($image->path, $search + 1, $length);

            $obj['img_name'] = $name;

            $path = public_path() .'/'. $image->path;
            $size = filesize($path);

            $obj['img_size'] = $size;
            $result[] = $obj;
        }

        return $result;
    }


    public function getList()
    {
        return Wish::all();
    }

    public function getListByCategory($name)
    {
        try{

            $category = \DB::table('category')->where('name', $name)->first();
            //$wishs = Wish::where('category_id', '=', $category->id)->get();
            $wishs = \DB::table('wish')->where('category_id', $category->id)->get();
            return $wishs;
        }
        catch(Exception $e)
        {
            return null;
        }
    }

    public function getListBySearch($search)
    {
        try{
            $wishs = Wish::where('description', 'LIKE', "%$search%")->get();
            return $wishs;
        }
        catch(Exception $e)
        {
            return null;
        }
    }

    public function getListByCategorySearch($category, $search)
    {
        try{
            $category = \DB::table('category')->where('name', $category)->first();
            $wishs = Wish::where('description', 'LIKE', "%$search%")
                            ->where('category_id', '=', $category->id)->get();
            //$wishs = \DB::table('wish')->where('category_id', $category->id)->get();
            return $wishs;
        }
        catch(Exception $e)
        {
            return null;
        }
    }

    public function copy($wishId, $listId)
    {
        try{
            $wish = new Wish();
            $wishOriginal = Wish::find($wishId);
            $wish->description = $wishOriginal->description;
            $wish->reference = $wishOriginal->reference;
            $wish->price = $wishOriginal->price;
            $wish->list_id = $listId;
            $wish->location_id = $wishOriginal->location_id;
            $wishStatus = new WishStatus($wish->id, 0);

            return $wish;
        }
        catch(Exception $e)
        {
            return null;
        }
    }

    public function changeStatus($id)
    {
        try{
            \DB::table('wish_status')
                ->where('wish_id', $id)
                ->update(array('status' => 0));
            return 1;
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    public function newWishStatus($wishId, $userStatus)
    {
        $wishStatus = new WishStatus();
        $manager = new WishStatusManager($wishStatus, array('wish_id' => $wishId, 'status' => $userStatus));
        $manager->save();
        return $wishStatus;
    }

    public function newWishDefaultImage($wishId, $userId)
    {
        $public_path = public_path();
        $wishImage = new WishImage();
        $wishImage->setUserId($userId);
        $wishImage->setWishId($wishId);
        $wishImage->setTmp(false);
        $this->destinationImagePath = $wishImage->getDestinationPath();

        $origin = $public_path.'/'.$wishImage->getDefaultImage();
        $destination  = $public_path.'/'.$this->destinationImagePath.'/wish.jpg';

        $upload = copy($origin, $destination);
    }

    public function newWishImage($image, $userId)
    {
        $wishImage = new WishImage();
        $wishImage->setUserId($userId);
        $wishImage->setTmp(true);
        $this->destinationImagePath = $wishImage->getDestinationPath();
        //$filename = str_random(12);
        $filename = $image->getClientOriginalName();
        //$extension =$file->getClientOriginalExtension();
        $upload = $image->move($this->destinationImagePath, $filename);
        return $upload;
    }

    public function moveWishTempImage($userId, $wishId)
    {
        $total = 0;
        $public_path = public_path();
        $wishImage = new WishImage();
        $wishImage->setUserId($userId);
        $wishImage->setWishId($wishId);
        $wishImage->setTmp(true);

        $temp_path = $public_path.'/'.$wishImage->getDestinationPath();
        $wishImage->setTmp(false);
        $real_path = $public_path.'/'.$wishImage->getDestinationPath();
        $move = false;

        $dir = opendir($temp_path);

        while ($archivo = readdir($dir))
        {
            if($archivo != "." && $archivo != "..")
            {
                $move = copy($temp_path.'/'.$archivo , $real_path.'/'.$archivo);
                $total = $total + 1;
                $this->createWishThumbImage($real_path, $archivo);
                $wishImage->setDestinationPath($real_path.'/'.$archivo);
                $wishImage->setImageName($archivo);
                $image_path = $wishImage->getDestinationPath().'/'.$wishImage->getImageName();
                $thumb_path = $wishImage->getDestinationPath().'/tb_'.$wishImage->getImageName();
                $this->saveWishPathImage($wishId, $image_path, $thumb_path);
            }
        }

       // if the user dont charge any image, then charge the default
        if ($total == 0)
        {
            $this->newWishDefaultImage($wishId, $userId);
            //$wishImage->setDestinationPath($wishImage->getDestinationPath().$wishImage->getImageName());
            $image_path = $wishImage->getDestinationPath().'/'.$wishImage->getImageName();
            $thumb_path = $wishImage->getDestinationPath().'/tb_'.$wishImage->getImageName();
            $this->saveWishPathImage($wishId, $image_path, $thumb_path);
            $move = true;
        }
        else{
            // delete temp files
            $this->deleteWishTempImages($temp_path);
        }

        return $move;
    }

    public function moveWishCopyImage($userId, $wishId, $wishIdOrig)
    {
        try{
            $total = 0;
            $public_path = public_path();
            $wishImage = new WishImage();
            $wishImage->setUserId($userId);
            $wishImage->setWishId($wishId);
            $wishImage->setTmp(false);
            // get original source images
            $wishImagesOriginal = $this->wishImagesById($wishIdOrig);
            $move = true;

            foreach ($wishImagesOriginal as $wishImageOriginal)
            {
                // the path of the source wish image
                $image_path = $public_path.'/'.$wishImageOriginal->path;
                $thumb_path = $public_path.'/'.$wishImageOriginal->thumb_path;

                // the destiny new wish path
                $real_path = $public_path.'/'.$wishImage->getDestinationPath();
                // cut the name of the image stored in the original wish path
                $search = '/';
                $lastOccurrence = strripos($image_path, $search);
                $large = strlen($image_path);
                $imageName = substr($image_path, ($lastOccurrence+1), ($large-$lastOccurrence));
                $new_image_path = $wishImage->getDestinationPath().'/'.$imageName;
                $new_thumb_path = $wishImage->getDestinationPath().'/'.'tb_'.$imageName;

                // copy source wish images to the new clone wish
                $move = copy($image_path , $real_path.'/'.$imageName);
                $move = copy($thumb_path , $real_path.'/tb_'.$imageName);

                $this->saveWishPathImage($wishId, $new_image_path, $new_thumb_path);
            }

            return $move;
        }
        catch (Exception $e)
        {
            return 0;
        }
    }

    public function saveWishPathImage($wishId, $path, $thumb_path)
    {
        // save the path in bd
        $wishImage = new WishImage();
        $manager = new ImageManager($wishImage, array('path' => $path, 'thumb_path' => $thumb_path, 'wish_id' => $wishId));
        $manager->save();
    }

    public function createWishThumbImage($path, $file)
    {
        $image = new SimpleImage($path .'/'. $file);
        // Create a squared version of the image
        $image->square(200);
        $image->save($path.'/'.'tb_'.$file);
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

    public function afterUpdate($wishId)
    {
        // check for new images
        $wishImage = WishImage::where('wish_id', '=', $wishId)->get();
        $result  = array();

        // look in the db to compare with the images in the temp directory
        foreach ( $wishImage as $image ) {
            $obj['img'] = asset($image->path);
            $obj['img_thumb'] = asset($image->thumb_path);

            $search = strripos($image->path, '/');
            $length = strlen($image->path);
            $name = substr($image->path, $search + 1, $length);

            $obj['img_name'] = $name;

            $path = public_path() .'/'. $image->path;
            $size = filesize($path);

            $obj['img_size'] = $size;
            $result[] = $obj;
        }

        return $result;
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

    public function checkForNewImages($wishId, $userId)
    {
        try {
            $public_path = public_path();
            $wishImages = WishImage::where('wish_id', '=', $wishId)->get();

            // at least one image ever exists before update...
            if (!$wishImages->isEmpty())
            {
                $wishImages[0]->setUserId($userId);
                $wishImages[0]->setWishId($wishId);
                $wishImages[0]->setTmp(true);
                $temp_path = $public_path.'/'.$wishImages[0]->getDestinationPath();
                $wishImages[0]->setTmp(false);
                $real_path = $public_path.'/'.$wishImages[0]->getDestinationPath();

                // db saved img names
                $dbNames = [];

                foreach ($wishImages as $wishImage)
                {
                    $search = strripos($wishImage->path, '/');
                    $length = strlen($wishImage->path);
                    $name = substr($wishImage->path, $search + 1, $length);
                    array_push($dbNames, $name);
                }

                $dir = opendir($temp_path);

                while ($archivo = readdir($dir))
                {
                    if($archivo != "." && $archivo != "..")
                    {
                        if (!in_array($archivo, $dbNames))
                        {
                            $wishImage = new WishImage();
                            $wishImage->setUserId($userId);
                            $wishImage->setWishId($wishId);
                            $move = copy($temp_path.'/'.$archivo , $real_path.'/'.$archivo);
                            $this->createWishThumbImage($real_path, $archivo);
                            $wishImage->setDestinationPath($real_path.'/'.$archivo);
                            $wishImage->setImageName($archivo);
                            $image_path = $wishImage->getDestinationPath().'/'.$wishImage->getImageName();
                            $thumb_path = $wishImage->getDestinationPath().'/tb_'.$wishImage->getImageName();
                            $this->saveWishPathImage($wishId, $image_path, $thumb_path);
                        }
                    }
                }
                $this->deleteWishTempImages($temp_path);
            }
            else { // if none images ever exist in the wish
                $this->moveWishTempImage($userId, $wishId);
            }
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
            $wishs = \Wish::all();
            return $wishs;
        }
        catch(Exception $e)
        {
            return true;
        }
    }

    public function wishImagesById($wishId)
    {
        $wishImages = WishImage::where('wish_id', '=', $wishId)->get();
        return $wishImages;
    }

} 