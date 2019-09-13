<?php

namespace Tests\Unit;

use App\User;
use Faker\Factory;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    //use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function a_user_can_be_added_by_the_admin()
    {

        $this->adminLogin(1);
        $faker = Factory::create();
        $name = $faker->name;
        $email = $faker->email;

        $response = $this->post('manage/user/create',
            [
                'name' => $name,
                'email' => $email
            ]);

        $this->assertEquals(200, $response->json(['status']));
    }

    private function adminLogin($id)
    {
        Auth::loginUsingId($id);
    }

    /** @test */
    public function a_user_name_should_be_unique()
    {
        $this->adminLogin(1);
        $faker = Factory::create();
        $email = $faker->email;
        $response = $this->post('manage/user/create',
            [
                'name' => $this->getExistingName(),
                'email' => $email,
            ]);

        $this->assertArrayHasKey('name', $response->json(['errors']));
    }

    private function getExistingName()
    {
        return User::where('is_admin', 0)->first()->name;
    }

    /** @test */
    public function a_user_email_should_be_valid()
    {

        $this->adminLogin(1);
        $faker = Factory::create();
        $name = $faker->name;
        $response = $this->post('manage/user/create',
            [
                'name' => $name,
                'email' => $name,
            ]);

        $this->assertArrayHasKey('email', $response->json(['errors']));
    }

    /** @test */
    public function only_admin_can_create_user()
    {

        $this->adminLogin(2);
        $faker = Factory::create();
        $name = $faker->name;
        $email = $faker->email;
        $response = $this->post('manage/user/create',
            [
                'name' => $name,
                'email' => $email,
            ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

}
