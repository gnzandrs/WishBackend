<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entities\Wish;
use App\Models\Entities\WishImage;
use App\Models\Managers\WishManager;
use App\Models\Repositories\WishRepo;
use App\Models\Repositories\CategoryRepo;
use JWTAuth;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WishController extends Controller
{
    protected $wishRepo;
    protected $categoryRepo;

    public function __construct(WishRepo $wishRepo, CategoryRepo $categoryRepo)
    {
      $this->wishRepo = $wishRepo;
      $this->categoryRepo = $categoryRepo;
    }

    // change status to buy it or available
    public function changeStatus()
    {
      try {
          if (Auth::check())
          {
              $id = Input::get('id');
              return $this->wishRepo->changeStatus($id);
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          Log::error('WishController changeStatus(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // copy the entity and images to a new wish
    public function copy()
    {
      try {
          if (Auth::check())
          {
              $wishIdOrig = Input::get('wishId');
              $listId = Input::get('wishListId');

              $wish = $this->wishRepo->copy($wishIdOrig, $listId);

              if (Request::ajax())
              {
                  if($wish->save())
                  {
                      return $this->wishRepo->afterCopy($wish, $wishIdOrig);
                  }
                  else{
                      return 0;
                  }
              }
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          Log::error('WishController copy(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    /**
     * Create a temporal directory to store the wishes's images
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response $response
     */
    public function createWishDirectory(Request $request)
    {
        try {
            $input = $request->all();
            $user = JWTAuth::toUser($input['token']);
            $wishListId = $input['wishListId'];
            $wishId = $input['wishId'];

            $this->wishRepo->createTempImageDirectory($user->id, $wishListId, $wishId);
            return ['created' => true];
        }
        catch (Exception $e)
        {
            return 0;
        }
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $user = JWTAuth::toUser($input['token']);
            $wish = $this->wishRepo->newWish();
            $wish->date = date('Y-m-d H:i:s');
            $manager = new WishManager($wish, $input['wish']);

            if ($manager->save())
            {
                if ($this->wishRepo->createWishDirectory($wish))
                {
                    return ['created' => true];
                    //return $wish->id;
                }

                return ['created' => false];
            }
            else {
                return ['error' => $wish->errors];
            }
        }
        catch (Exception $e)
        {
            //Log::error('WishController store(): '.$e);
            //$this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
            return 0;
        }
    }

    // delete wish
    public function destroy()
    {
      try {
          if (Auth::check())
          {
              $id = Input::get('wishId');
              $wish = Wish::find($id);
              if (is_null ($wish))
              {
                  App::abort(404);
              }
              return $this->wishRepo->deleteWish($wish);
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          Log::error('WishController destroy(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // return edit's view
    public function edit()
    {
      try {
          if (Auth::check())
          {
              $id = Input::get('wish_id');
              $wish = $this->wishRepo->find($id);
              $categories = $this->categoryRepo->getCategories();
              return View::make('wishlist/wishedit', compact('wish', 'categories'));
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          Log::error('WishController edit(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // delete the image from server
    public function imageDelete(Request $request)
    {
      try {
          $input = $request->all();
          $userId = JWTAuth::toUser($input['token'])->id;
          $imageName = $input['imgName'];
          $imgId = $input['imgId'];
          $wishId = $input['wishId'];

          if ($imgId > 0)
          {
              $delete = $this->wishRepo->deleteImageFromWish($userId, $imageName, $wishId, $imgId);
          }
          else {
              $delete = $this->wishRepo->deleteImageFromTemp($userId, $imageName);
          }

          return json_encode($delete);
      }
      catch (Exception $e)
      {
          //Log::error('WishController imageUpload(): '.$e);
          //$this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return $e;
      }
    }

    // delete all temp images from server, this is used to clean the temp directory
    public function imagesDelete()
    {
      try {
          if (Auth::check())
          {
              $userId = Auth::user()->id;
              $delete = $this->wishRepo->deleteWishUserTempImages($userId);
              return json_encode($delete);
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          //Log::error('WishController imageUpload(): '.$e);
          //$this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return $e;
      }
    }

    // return the list's images of the wish
    public function imageList()
    {
      try {
          if (Auth::check())
          {
              $wishId = Input::get("id");
              $wishImage = $this->wishRepo->getImages($wishId);
              return json_encode($wishImage);
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          //Log::error('WishController imageUpload(): '.$e);
          //$this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return $e;
      }
    }

    // upload the wish's image
    public function imageUpload(Request $request)
    {
      try {
          $input = $request->all();
          $user = JWTAuth::toUser($input['token']);
          $wishListId = $input['wishListId'];
          //$file = $input['file'];
          $file = $request->file('file');

          $upload_success = $this->wishRepo->newWishImage($file, $wishListId, $user->id);

          if( $upload_success ) {
              return ['success' => 200];
          } else {
              return ['error' => 400];
          }
      }
      catch (Exception $e)
      {
          Log::error('WishController imageUpload(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }

    }

    // receive and charge the image into the wish's folder
    public function index()
    {
      try {
          return Redirect::route('home');
      }
      catch (Exception $e)
      {
          Log::error('WishController index(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // search and wish and return the result view
    public function search($search)
    {
      try {
          $wishs = $this->wishRepo->getListBySearch($search);

          if (Request::ajax())
          {
              return View::make('category/wishlist', compact('wishs'));
          }
      }
      catch (Exception $e)
      {
          Log::error('WishController search($search): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // latest wishs added view
    public function latestAdded()
    {
      try {
          if (Auth::check())
          {
              if (Request::ajax())
              {
                  $wishs = $this->wishRepo->latestAdded();
                  return View::make('wish/latest', compact('wishs'));
              }
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          Log::error('WishController latestAdded(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // show a wish
    public function show($id)
    {
      try {
          $wish = $this->wishRepo->find($id);
          return View::make('wish/show', compact('wish'));
      }
      catch (Exception $e)
      {
          Log::error('WishController show($id): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // update wish
    public function update()
    {
      try {
          if (Auth::check())
          {
              $id = Input::get('id');
              $wish = $this->wishRepo->find($id);
              $manager = new WishManager($wish, Input::all());
              $userId =  $wish->wishlist->user->id;
              if($manager->save())
              {
                  $this->wishRepo->checkForNewImages($id, $userId);
                  return $wish->id;
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
          //Log::error('WishController update(): '.$e);
          //$this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // return the wishlist's view for a id
    public function wishList()
    {
      try {
          $id = Input::get('id');
          $wishs = DB::table('wish')->where('list_id', $id)->get();

          if (Request::ajax())
          {
              return View::make('wish/wishlist', compact('wishs'));
          }

      }
      catch (Exception $e)
      {
          Log::error('WishController wishList(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }

    // wishlist modal
    public function wishListModal()
    {
      try {
          $id = Input::get('id');
          $wishs = DB::table('wish')->where('list_id', $id)->get();

          if (Request::ajax())
          {
              return View::make('utils/modal/wish/wishlist', compact('wishs'));
          }
      }
      catch (Exception $e)
      {
          Log::error('WishController wishListModal(): '.$e);
          $this->logRepo->newLog('WishController.php', 'WishController.php', 'error catch', $e);
          return 0;
      }
    }
}
