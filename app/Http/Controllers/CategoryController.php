<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entities\Wish;
use App\Models\Entities\Log;
use App\Models\Repositories\WishRepo;
use App\Models\Repositories\CategoryRepo;
use App\Models\Repositories\LogRepo;

class CategoryController extends Controller
{
    protected $categoryRepo;
    protected $wishRepo;
    protected $logRepo;

    public function __construct(CategoryRepo $categoryRepo, WishRepo $wishRepo, LogRepo $logRepo)
    {
        $this->categoryRepo = $categoryRepo;
        $this->wishRepo = $wishRepo;
        $this->logRepo = $logRepo;
    }

    /**
     * Get all the categories
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategories()
    {
      try {
          return $this->categoryRepo->getList();
      }
      catch (Exception $e)
      {
          \Log::error('CategoryController getCategories(): '.$e);
          $this->logRepo->newLog('CategoryController.php', 'CategoryController.php', 'catch', $e);
          return 'error';
      }
    }

    /**
     * Search the record by one category and typed text
     *
     * @param  string  $category
     * @param  string  $search
     * @return \Illuminate\Http\Response
     */
    public function searchByCategory($category, $search)
    {
      try{
          \Log::info("CategoryController searchByCategory($category, $search)");
          $wishs = $this->wishRepo->getListByCategorySearch($category, $search);
          return $wishs->ToJson();
      }
      catch(Exception $e)
      {
          \Log::error('CategoryController searchByCategory($category, $search): '.$e);
          $this->logRepo->newLog('CategoryController.php', 'CategoryController.php', 'catch', $e);
          return 'error';
      }

    }

    /**
     * All wishes related to a category
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
      try{
          $wishs = $this->wishRepo->getListByCategory($name);
          return $wishs;
      }
      catch(Exception $e)
      {
          \Log::error('CategoryController show($name): '.$e);
          $this->logRepo->newLog('CategoryController.php', 'CategoryController.php', 'catch', $e);
          return 'error';
      }
    }
}
