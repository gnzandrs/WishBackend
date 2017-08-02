<?php

namespace App\Models\Repositories;

use App\Models\Entities\WishList;
use App\Models\Entities\Wish;
use App\Models\Entities\WishStatus;

class WishListRepo extends BaseRepo {

    /**
     * Create the directory on the server to image storage
     * for wish that will be include on it
     * @param  \app\Models\Entities\WishList $wishList
     * @param  int  $wishIdTmp
     * @return \app\Models\Entities\Wish $wish
     */
    public function createDirectoryStructure($wishList)
    {
        try{
            $this->createDirectoryTree($wishList);
            return 1;
        }
        catch (Exception $e)
        {
            return 0;
        }

    }

    public function createDirectoryTree($wishList)
    {
        try{
            $public_path = public_path();
            $user = $wishList->User;

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

    public function getModel()
    {
        return new WishList;
    }

    public function newWishList()
    {
        $wishlist = new WishList();
        return $wishlist;
    }

    public function updateWishList($wishlist, $data)
    {
        $wishlist = $this->wishlistRepo->find($id);
        if (is_null ($wishlist))
        {
            App::abort(404);
        }
        $wishlist->id =  id;

    }

    public function deleteWishList($id)
    {
        // first the references
        $wishs = \DB::table('wish')->where('list_id', $id)->get();
        foreach ($wishs as $wish)
        {
            \DB::table('wish_image')->where('wish_id', '=', $wish->id)->delete();
            \DB::table('wish_status')->where('wish_id', '=', $wish->id)->delete();
        }

        \DB::table('wish')->where('list_id', '=', $id)->delete();

        $wishlist = WishList::find($id);
        $wishlist->delete();
        return 1;
    }

    public function newWish()
    {
        $wish = new Wish();
        $wishStatus = new WishStatus($wish->id, 0);
        return $wish;
    }

    public function wishListByUser($id)
    {
        $wishlists = \DB::table('list')->where('user_id', '=', $id)->get();
        return $wishlists;
    }
} 