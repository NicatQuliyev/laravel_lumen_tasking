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

use App\Http\Controllers\TasksController;


/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['prefix' => 'api/v1', 'middleware' => 'jwt.auth'], function () use ($router) {

    /*
     * Tasks
     */
    $router->post('tasks', 'TasksController@storeTask');
    $router->get('tasks/{id}', 'TasksController@taskByid');
    $router->get('tasks', 'TasksController@getTasks');
    $router->put('tasks', 'TasksController@updateTask');
    $router->delete('tasks/{id}', 'TasksController@deleteTask');

    /*
     * Tags
     */
    $router->get('tags', 'TagsController@getTags');
    $router->get('tag', 'TagsController@getTag');

    /*
     * Projects
     */
    $router->get('projects', 'ProjectsController@getProjects');

    /*
     * Users
     */
    $router->get('user', 'AuthController@user');

});

    /*
    * Authentication - Registration for users
    */
$router->group(['prefix' => 'auth'], function () use ($router){
    $router->post('login', 'AuthController@authenticate');
    $router->post('register', 'AuthController@register');
});