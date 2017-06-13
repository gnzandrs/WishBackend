<?php

namespace App\Models\Repositories;

//use Whoops\Example\Exception;
use App\Models\Entities\Category;

class CategoryRepo extends BaseRepo {

    public function getModel()
    {
        return new Category;
    }

    public function getCategories()
    {
        return Category::all();
    }

}
