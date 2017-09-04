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
use JWTAuth;

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

    /**
     * Create a temporal directory to store the wishlist's images
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $response
     */
    public function createImageDirectory(Request $request)
    {
        try {
            $input = $request->all();
            $user = JWTAuth::toUser($input['token']);
            $wishListId = $input['wishListId'];

            $this->wishlistRepo->createTempImageDirectory($wishListId, $user->id);
            return ['created' => true];
        }
        catch (Exception $e)
        {
            return 0;
        }
    }

    /**
     * Create a newly wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $user = JWTAuth::toUser($input['token']);
            $wishList = $input['wishList'];

            $newWishList = $this->wishlistRepo->newWishList();
            $newWishList->user_id = $user->id;
            $newWishList->name = $wishList['name'];
            $newWishList->occasion = 'no one';
            $newWishList->followers = 0;
            $newWishList->notification = 0;
            $newWishList->password = 'none';
            $manager = new WishListManager($newWishList, $wishList);

            if ($manager->save())
            {
                $this->wishlistRepo->createDirectoryStructure($newWishList);
                return $newWishList->id;
            }
            else {
                return ['created' => false];
            }
        }
        catch (Exception $e)
        {
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

    /**
     * Get a wishlist by id
     * @param  int $id
     * @return \Illuminate\Http\Response $response
     */
    public function show($id)
    {
        try {
            $wishList = $this->wishlistRepo->find($id);
            $wishList->wishs;
            return $wishList;
        }
        catch (Exception $e)
        {
            //Log::error('WishListController show(): '.$e);
            //$this->logRepo->newLog('WishListController.php', 'WishListController.php', 'error catch', $e);
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
