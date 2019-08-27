<?php
use Illuminate\Routing\Router;
use Illuminate\Http\Response;

$router->get('/', function () {
    return ['status' => 'running'];
});

$router->group(['namespace' => 'App\Controllers', 'prefix' => 'recipes'], function (Router $router) {
    $router->get('/', [
        'name' => 'recipes.index',
        'uses' => 'RecipesController@index'
    ]);
    $router->post('/', [
        'name' => 'recipes.create',
        'middleware' => 'App\Middleware\Authorize::class',
        'uses' => 'RecipesController@create'
    ]);
    $router->get('/{id}', [
        'name' => 'recipes.show',
        'uses' => 'RecipesController@show'
    ]);
    $router->delete('/{id}', [
        'name' => 'recipes.destory',
        'middleware' => 'App\Middleware\Authorize::class',
        'uses' => 'RecipesController@destory'
    ]);
    $router->put('/{id}', [
        'name' => 'recipes.update',
        'middleware' => 'App\Middleware\Authorize::class',
        'uses' => 'RecipesController@update'
    ]);
    $router->post('/{id}/rating', [
        'name' => 'ratings.create',
        'uses' => 'RatingsController@create'
    ]);
});

$router->group(['namespace' => 'App\Controllers', 'prefix' => 'users'], function (Router $router) {
    $router->post('/', ['name' => 'users.index', 'uses' => 'UsersController@create']);
    $router->post('/login', ['name' => 'users.login', 'uses' => 'UsersController@login']);
});

// catch-all route
$router->any('{any}', function () {
    return response(['error' => 404], 404);
})->where('any', '(.*)');
