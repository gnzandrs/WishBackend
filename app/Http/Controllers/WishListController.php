<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entities\WishList;
use App\Models\Entities\Wish;
use App\Models\Managers\WishListManager;
use App\Models\Managers\WishManager;
use App\Models\Repositories\WishListRepo;
use App\Models\Repositories\WishRepo;
use App\Models\Repositories\CategoryRepo;
use App\Models\Repositories\UserRepo;

class WishListController extends Controller
{
    protected $wishlistRepo;
    protected $wishRepo;
    protected $categoryRepo;
    protected $userRepo;

    public function __construct(WishListRepo $wishlistRepo, WishRepo $wishRepo,
                                CategoryRepo $categoryRepo, UserRepo $userRepo)
    {
        $this->wishlistRepo = $wishlistRepo;
        $this->wishRepo = $wishRepo;
        $this->categoryRepo = $categoryRepo;
        $this->userRepo = $userRepo;
    }

    // create wishlist view
    public function create()
    {
        try {
            if (Auth::check())
            {
                return View::make('wishlist/create');
            }
            else{
                return Redirect::route('user/login');
            }
        }
        catch (Exception $e)
        {
            Log::error('WishListController create(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // create a wishlist with a wish from id
    public function createWithWish()
    {
        try {
            if (Auth::check())
            {
                $wishId = Input::get('wishId');
                $wishOrig = $this->wishRepo->find($wishId);

                // create the list and add the wish...
                $wishlist = $this->wishlistRepo->newWishList();
                $wishlist->user_id = Auth::user()->id;
                $wishlist->name = "Lista de " . Auth::user()->name;

                if ($wishlist->save())
                {
                    $wish = $this->wishRepo->copy($wishOrig->id, $wishOrig->WishList->id);
                    $wish->list_id = $wishlist->id;

                    if($wish->save())
                    {
                        $this->wishRepo->afterCopy($wish, $wishOrig->id);
                        return View::make('utils/modal/wishlist/edit', compact('wishlist'));
                    }
                    else{
                        return 0;
                    }
                }
                else{
                    return 0;
                }
            }
            else{
                return Redirect::route('user/login');
            }

        }
        catch (Exception $e)
        {
            Log::error('WishListController createWithWish(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // destroy object in db
    public function destroy()
    {
        try {
            $id = Input::get('ListId');
            return $this->wishlistRepo->deleteWishList($id);
        }
        catch (Exception $e)
        {
            Log::error('WishListController destroy(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // edit wishlist view
    public function edit($id)
    {
        try {
            if (Auth::check())
            {
                $wishlist = $this->wishlistRepo->find($id);
                return View::make('wishlist/edit', compact('wishlist', 'popUp'));
            }
            else{
                return Redirect::route('user/login');
            }
        }
        catch (Exception $e)
        {
            Log::error('WishListController edit($id): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // index accion
    public function index()
    {
        try {
            return Redirect::route('home');
        }
        catch (Exception $e)
        {
            Log::error('WishListController index(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // show wish list view
    public function show()
    {
        try {
            $userId = Auth::user()->id;
            $wishlists = $this->userRepo->wishLists($userId);
            return View::make('wishlist/show', compact('wishlists'));
        }
        catch (Exception $e)
        {
            Log::error('WishListController show(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // store object in db
    public function store()
    {
        try {
            if (Auth::check())
            {
                $wishlist = $this->wishlistRepo->newWishList();
                $wishlist->user_id = Auth::user()->id;
                $manager = new WishListManager($wishlist, Input::all());
                if ($manager->save())
                {
                    return $wishlist->id;
                }
                else{
                    return 0;
                }
            }
            else{
                return Redirect::route('user/login');
            }
        }
        catch (Exception $e)
        {
            Log::error('WishListController store(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // update the object
    public function update()
    {
        try {
            if (Auth::check())
            {
                $id = Input::get('WishListId');
                $wishlist = $this->wishlistRepo->find($id);
                $manager = new WishListManager($wishlist, Input::all());
                if ($manager->save())
                {
                    return $wishlist->id;
                }
                else{
                    return 0;
                }
            }
            else{
                return Redirect::route('user/login');
            }

        }
        catch (Exception $e)
        {
            Log::error('WishListController update(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // all the lists for a specific user
    public function wishListByUser()
    {
        try {
            $id = Auth::user()->id;
            $wishlists = $this->wishlistRepo->wishListByUser($id);

            if (Request::ajax())
            {
                return View::make('utils/menu/wish/menu', compact('wishlists'));
            }

        }
        catch (Exception $e)
        {
            Log::error('WishListController wishListByUser(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }

    // wish view for the modal
    public function wishShow()
    {
        try {
            $wish = $this->wishRepo->newWish();
            $wish->list_id = Input::get('id'); // wish list id
            $categories = $this->categoryRepo->getCategories();
            return View::make('wishlist/wish', compact('wish', 'categories'));
        }
        catch (Exception $e)
        {
            Log::error('WishListController wishShow(): '.$e);
            $this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
            return 0;
        }
    }
}
