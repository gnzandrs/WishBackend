<?php

namespace App\Models\Repositories;

use App\Models\Entities\WishList;
use App\Models\Entities\Wish;
use App\Models\Entities\WishStatus;

class WishListRepo extends BaseRepo {

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