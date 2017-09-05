<?php

namespace App\Models\Repositories;

use App\Models\Entities\WishList;
use App\Models\Entities\Wish;
use App\Models\Entities\WishStatus;

class WishListRepo extends BaseRepo {

    /**
     * Move the images stored on the temporaly folder
     * to the real location.
     * @param  \app\Models\Entities\WishList $wishList
     * @return int $result
     */
    public function moveImagesFromTemp($wishList)
    {
        try{

            return 1;
        }
        catch (Exception $e)
        {
            return 0;
        }
    }

    /**
     * Create a temporaly directory to save the images
     * from wishes that belong to a temporaly wishlist
     * before store it on the bd.
     * @param  int  $tmpWishId
     * @return int $result
     */
    public function createTempImageDirectory($tmpWishId, $userId)
    {
        try {
            $public_path = public_path();
            $baseWishListFolder = $public_path."/assets/user/".$userId."/tmp/wishlist";
            $tempFolder = $baseWishListFolder.'/'.$tmpWishId;

            if (!file_exists($baseWishListFolder))
            {
                mkdir($baseWishListFolder, 0700);
            }

            if (!file_exists($tempFolder))
            {
                mkdir($tempFolder, 0700);
            }

            return 1;
        }
        catch (exception $e)
        {
            return 0;
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