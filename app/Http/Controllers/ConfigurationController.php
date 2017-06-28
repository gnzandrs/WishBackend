<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entities\User;
use App\Models\Entities\Country;
use App\Models\Entities\City;
use App\Models\Managers\RegisterManager;
use App\Models\Managers\OptionManager;
use App\Models\Managers\UserManager;
use App\Models\Repositories\UserRepo;
use App\Models\Repositories\ConfigurationRepo;
use App\Models\Entities\Log;
use App\Models\Repositories\LogRepo;

class ConfigurationController extends Controller
{
    protected $configRepo;
    protected $userRepo;
    protected $logRepo;

    public function __construct(UserRepo $userRepo, ConfigurationRepo $configRepo, LogRepo $logRepo)
    {
      $this->configRepo = $configRepo;
      $this->userRepo = $userRepo;
      $this->logRepo = $logRepo;
    }

    /**
     * Edit's view of user avatar
     *
     * @return \Illuminate\Http\Response
     */
    public function editAvatar()
    {
      try {
          return View::make('user/editavatar');
      }
      catch (Exception $e)
      {
          Log::error('ConfigurationController editAvatar(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

    /**
     * Redirect to index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      try {
          Log::info("ConfigurationController index");
          return Redirect::route('home');
      }
      catch (Exception $e)
      {
          Log::error('ConfigurationController index(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

    /**
     * Cut image view
     *
     * @return \Illuminate\Http\Response
     */
    public function imageCrop()
    {
      try {
          $userId = Auth::user()->id;
          $userImage = $this->configRepo->userImageById($userId);
          return View::make('utils/modal/user/cropimage', compact('userImage'));
      }
      catch (Exception $e)
      {
          //Log::error('ConfigurationController imageCrop(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

    /**
     * Apply cut to image
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function imageCropApply()
    {
      try {
          $x =  Input::get('x');
          $y =  Input::get('y');
          $w =  Input::get('w');
          $h =  Input::get('h');
          $width = Input::get('width'); // original width
          $height = Input::get('height'); // original height
          $userId = Auth::user()->id;
          $result = $this->configRepo->userImageCropApply($userId, $width, $height, $x, $y, $w, $h);

          if (Request::ajax())
          {
              if($result)
              {
                  return 1;
              }
              else{
                  return 0;
              }
          }
      }
      catch (Exception $e)
      {
          //Log::error('ConfigurationController imageCropApply(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

    /**
     * Upload user's profile image
     *
     * @return \Illuminate\Http\Response
     */
    public function imageUploadProfile()
    {
      try {
          if (Auth::check())
          {
              $file = Input::file('file');
              $upload_success = $this->configRepo->newUserImage($file, Auth::user()->id);

              if( $upload_success ) {
                  return Response::json('success', 200);
              } else {
                  return Response::json('error', 400);
              }
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          //Log::error('ConfigurationController imageUploadProfile(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

    /**
     * User's options view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function option()
    {
      try {
          if (Auth::check())
          {
              $id = Auth::user()->id;
              $user = $this->userRepo->find($id);
              $config = $user->configuration;
              return View::make('user/options', compact('config', 'user'));
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          Log::error('ConfigurationController option(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

    //
    public function privacy()
    {

    }

    /**
     * User's profile view
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
      try {
          if (Auth::check())
          {
              $id = Auth::user()->id;
              $user = $this->userRepo->find($id);
              //$conf = $this->userRepo->getConfig($userId);
              $config = $user->configuration;
              $userImage = $this->configRepo->userImageById($user->id);
              $countries = Country::all();
              $cities = City::all();
              return View::make('user/profile', compact('config', 'user', 'countries', 'cities', 'userImage'));
          }
          else{
              return Redirect::route('user/login');
          }
      }
      catch (Exception $e)
      {
          Log::error('ConfigurationController profile(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

    /**
     * Update user's option
     *
     * @return \Illuminate\Http\Response
     */
    public function updateOption()
    {
      try {
          $user = Auth::user();
          $configuration = $user->configuration;
          $manager = new OptionManager($configuration, Input::all());
          if (Request::ajax())
          {
              if($manager->save())
              {
                  return 1;
              }
              else{
                  return Redirect::back()->withInput()->withErrors($user->errors);
              }
          }
      }
      catch (Exception $e)
      {
          Log::error('ConfigurationController updateOption(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

    public function updatePrivacy()
    {

    }

    /**
     * Update user's profile
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile()
    {
      try {
          //Log::info('ConfigurationController updateProfile()');
          $user = Auth::user();
          $manager = new UserManager($user, Input::all());

          if (Request::ajax())
          {
              if($manager->save())
              {
                  return 1;
              }
              else{
                  return Redirect::back()->withInput()->withErrors($user->errors);
              }
          }
      }
      catch (Exception $e)
      {
          //Log::error('ConfigurationController updateProfile(): '.$e);
          $this->logRepo->newLog('ConfigurationController.php', 'ConfigurationController.php', 'error catch', $e);
          return 0;
      }
    }

}
