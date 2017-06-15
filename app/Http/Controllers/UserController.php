<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entities\User;
use App\Models\Entities\Country;
use App\Models\Entities\City;
use App\Models\Entities\WishList;
use App\Models\Entities\Log;
use App\Models\Managers\RegisterManager;
use App\Models\Managers\UserManager;
use App\Models\Repositories\UserRepo;
use App\Models\Repositories\ConfigurationRepo;
use App\Models\Repositories\WishListRepo;
use App\Models\Repositories\LogRepo;

class UserController extends Controller
{
    protected $userRepo;
    protected $configRepo;
    protected $wishlistRepo;
    protected $logRepo;

    public function __construct(UserRepo $userRepo, ConfigurationRepo $configRepo,
                              WishListRepo $wishlistRepo, LogRepo $logRepo)
    {
        $this->configRepo = $configRepo;
        $this->userRepo = $userRepo;
        $this->wishlistRepo = $wishlistRepo;
        $this->logRepo = $logRepo;
    }

    /**
     * Check username availability
     *
     * @return \Illuminate\Http\Response
     */
    public function check($username)
    {
        $available = $this->userRepo->userCheck($username);
        return $available;
    }

    /**
     * Check email existence
     *
     * @return \Illuminate\Http\Response
     */
    public function checkEmail($email)
    {
        $available = $this->userRepo->emailCheck($email);
        return $available;
    }

    /**
     * Get all for a specified country
     *
     * @return \Illuminate\Http\Response
     */
    public function citiesList(Request $request)
    {
        $countryCode = $request->input('code');
        $cities = \DB::table('city')
          ->join('country', 'city.country_id', '=', 'country.id')
          ->where('country.id', $countryCode)
          ->select('city.id', 'city.code', 'city.name')
          ->get();

        return $cities;
    }

    /**
     * Get all the countries availables
     *
     * @return \Illuminate\Http\Response
     */
    public function countriesList()
    {
        $countrys = Country::all();
        return $countrys;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        User::create($request->all());
        return ['created' => true];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return User::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return ['updated' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);
        return ['deleted' => true];
    }

    /**
     * Avatar image url of user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAvatarImage()
    {
        $userId = Auth::user()->id;
        $image = $this->userRepo->getAvatarImage($userId);
        if (Request::ajax())
        {
          return $image;
        }
    }

    /**
     * Wishlist of user by id
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function wishListShow($id)
    {
        $wishlist = $this->wishlistRepo->find($id);
        $wishlist->Wishs;
        $wishlist->User;
        return $wishlist;
    }
}
