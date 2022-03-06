<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Common routes
// Route::post('login', 'AuthController@login');
// Route::post('register', 'AuthController@register');

// Route::group(['middleware' => 'auth:api',
// ],
// function() {
//             Route::get('user', 'AuthController@user');
//             Route::put('users/info', 'AuthController@updateInfo');
//             Route::put('users/password', 'AuthController@updatePassword');
// });


// ===================== ADMIN NEW ============================================

Route::prefix('admin')->group(function(){
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout'); //logout

    Route::middleware(['auth:api', 'scope:admin'])->group(function(){
            Route::get('user', 'AuthController@user');
            Route::put('users/info', 'AuthController@updateInfo');
            Route::put('users/password', 'AuthController@updatePassword');

            Route::namespace('Admin')->group(function(){
                    //Route::post('logout', 'AuthController@logout'); //logout
                    Route::get('chart', 'DashboardController@chart');

                    Route::post('upload', 'ImageController@upload');
                    Route::get('export', 'OrderController@export');

                    Route::apiResource('users', 'UserController');
                    Route::apiResource('roles', 'RoleController');
                    Route::apiResource('products', 'ProductController');
                    Route::apiResource('orders', 'OrderController')->only('index','show');
                    Route::apiResource('permissions', 'PermissionController')->only('index');
            });
    });
});


//=====================END ADMIN NEW

//Admin old
// Route::group(['middleware' => ['auth:api', 'scope:admin'],
//                'prefix' => 'admin',
//                 'namespace' => 'Admin'],
//                 function() {
//     Route::post('logout', 'AuthController@logout');
//     Route::get('chart', 'DashboardController@chart');


//     Route::post('upload', 'ImageController@upload');
//     Route::get('export', 'OrderController@export');


//     Route::apiResource('users', 'UserController');
//     Route::apiResource('roles', 'RoleController');
//     Route::apiResource('products', 'ProductController');
//     Route::apiResource('orders', 'OrderController')->only('index','show');
//     Route::apiResource('permissions', 'PermissionController')->only('index');

// });


// ===================== INFLUENCER NEW ============================================

Route::prefix('influencer')->group(function(){
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');

    Route::get('products', 'Influencer\ProductController@index');


    Route::middleware(['auth:api', 'scope:influencer'])->group(function(){
            Route::get('user', 'AuthController@user');
            Route::put('users/info', 'AuthController@updateInfo');
            Route::put('users/password', 'AuthController@updatePassword');

            Route::namespace('Influencer')->group(function(){
                //Route::post('logout', 'AuthController@logout'); //logout
                Route::post('links', 'LinkController@store');
                Route::get('stats', 'StatsController@index');
                Route::get('rankings', 'StatsController@ranking');
            });
    });
});

// ===================== END INFLUENCER NEW ============================================

//Influencer old
// Route::group([
//                'prefix' => 'influencer',
//                 'namespace' => 'Influencer'],
//                 function() {
//                     Route::get('products', 'ProductController@index');

//                     Route::group([
//                         'middleware' => ['auth:api', 'scope:influencer'],
//                     ], function () {
//                             Route::post('links', 'LinkController@store');
//                             Route::get('stats', 'StatsController@index');
//                             Route::get('rankings', 'StatsController@ranking');
//                     });



// });



//CHeckout
Route::group([
    'prefix' => 'checkout',
     'namespace' => 'Checkout'],
     function() {
            Route::get('links/{code}', 'LinkController@show');
            Route::post('orders', 'OrderController@store');
            Route::post('orders/confirm', 'OrderController@confirm');
     });
