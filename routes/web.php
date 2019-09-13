<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/unauthorized', function () {
    return response(['status' => 401, 'message' => 'unauthorized']);
})->name('unauthorized');

Route::group([
    'prefix' => 'manage',
    'middleware' => ['auth', 'check.role:admin']
], function () {
    Route::post('/user/create', [
        'as' => 'admin.create_user',
        'uses' => 'Manage\UserController@store'
    ]);
    Route::get('/users', [
        'as' => 'admin.users',
        'uses' => 'Manage\UserController@users'
    ]);
    Route::get('transactions/debit', [
        'as' => 'admin.transactions',
        'uses' => 'Manage\TransactionsController@getDebitTransactions'
    ]);

});

Route::group([
    'middleware' => ['auth', 'check.role:user']
], function () {
    Route::post('/transaction/create', [
        'as' => 'transaction.create',
        'uses' => 'TransactionsController@store'
    ]);
});

