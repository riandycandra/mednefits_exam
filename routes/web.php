<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/role', 'RoleController@index');

$router->get('/user', 'UserController@index');
$router->get('/user/{username}', 'UserController@index');
$router->post('/user/register', 'UserController@register');
$router->post('/user/assign', 'UserController@assign');

$router->get('/clinic', 'ClinicController@index');
$router->post('/clinic/register', 'ClinicController@register');

$router->get('/booking', 'BookingController@index');
$router->post('/booking/start', 'BookingController@start');
$router->post('/booking/end', 'BookingController@end');