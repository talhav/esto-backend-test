<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TransactionManagementTest extends TestCase
{

    /** @test */
    public function a_debit_transaction_can_be_added_by_the_user()
    {
        $this->userLogin();
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('transaction/create',
                [
                    'type' => 'DEBIT',
                    'amount' => rand(1, 99)
                ]
            );
        }
        $this->assertEquals(200, $response->json(['status']));
    }

    private function userLogin()
    {
        $user_id = User::where('is_admin', 0)->orderBy('id', 'desc')->first()->id;
        Auth::loginUsingId($user_id);
    }

    /** @test */
    public function a_credit_transaction_can_be_added_by_the_user()
    {
        $this->userLogin();
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('transaction/create',
                [
                    'type' => 'CREDIT',
                    'amount' => rand(1, 99)
                ]
            );

            $this->assertEquals(200, $response->json(['status']));
        }
    }

    /** @test */
    public function amount_should_be_required()
    {
        $this->userLogin();
        $response = $this->post('transaction/create',
            [
                'type' => 'DEBIT',
                'amount' => '',
            ]);

        $this->assertArrayHasKey('amount', $response->json(['errors']));

    }

    /** @test */
    public function amount_should_be_greater_than_zero()
    {
        $this->userLogin();
        $response = $this->post('transaction/create',
            [
                'type' => 'DEBIT',
                'amount' => -21,
            ]);

        $this->assertArrayHasKey('amount', $response->json(['errors']));

    }

    /** @test */
    public function a_type_should_be_required()
    {
        $this->userLogin();
        $response = $this->post('transaction/create',
            [
                'type' => '',
                'amount' => 100,
            ]);

        $this->assertArrayHasKey('type', $response->json(['errors']));

    }

    /** @test */
    public function a_type_should_be_debit_or_credit()
    {
        $this->userLogin();
        $response = $this->post('transaction/create',
            [
                'type' => 'debits',
                'amount' => 100,
            ]);

        $this->assertArrayHasKey('type', $response->json(['errors']));

    }

    /** @test */
    public function transaction_only_added_by_logged_in_user()
    {

        $response = $this->post('transaction/create',
            [
                'type' => 'DEBIT',
                'amount' => 100,
            ]);


        $this->assertEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function get_user_transactions_only_by_admin()
    {
        $this->userLogin();

        $response = $this->get('manage/transactions/debit');

        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function get_user_debit_transactions()
    {
        $this->adminLogin();

        $response = $this->get('manage/transactions/debit');

        $this->assertEquals(200, $response->json(['status']));
    }

    private function adminLogin()
    {
        $user_id = User::where('is_admin', 1)->orderBy('id', 'desc')->first()->id;
        Auth::loginUsingId($user_id);
    }


}
