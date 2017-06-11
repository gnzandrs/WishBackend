<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    //use DatabaseMigrations;

    public function testUserCreate()
    {
        $data = $this->getData();
        $id = 1;
        // create user
        $this->json('post','/api/user', $data)
            ->assertJson(['created' => true]);

        $data = $this->getData(['name' => 'jane']);

        // update created user
        $this->json('put',"/api/user/$id", $data)
            ->assertJson(['updated' => true]);

        // assert the new name of user
        $this->json('get', "api/user/$id")
            ->assertJson(['name' => 'jane']);

        // delete user
        $this->json('delete', "api/user/$id")->assertJson(['deleted' => true]);
    }

    public function getData($custom = array())
    {
        $data = [
            'name'      => 'joe',
            'email'     => 'joe@doe.com',
            'password'  => '12345'
            ];
        $data = array_merge($data, $custom);
        return $data;
    }
}
