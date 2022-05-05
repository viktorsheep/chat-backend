<?php

use Carbon\Carbon;

// Root
$router->get('/', function () use ($router) {
    return Carbon::parse(Carbon::now())->timezone('Asia/Yangon')->toDateTimeString();
});

// File
$router->group(['middleware' => 'auth'], function ($router) {
  $router->get('{file}', function ($file) {
    return response()->download($file);
  });
});

// APIs
$router->group(['prefix' => 'api'], function () use ($router) {

  $router->get('test', function () use ($router) {
    return "hello";
  });
  
  // Middleware : Auth
  $router->group(['middleware' => 'auth'], function () use ($router) {

    // Users
    $router->group(['prefix' => 'user'], function ($router) {
      // $router->post('add', 'UserController@add');
      // $router->get('browse', 'UserController@browse');
      // $router->get('admins', 'UserController@admins');
      $router->get('users', 'UserController@users');
      $router->get('roles', 'UserController@roles');
      $router->put('edit/{id}', 'UserController@edit');
      $router->get('view/{id}', 'UserController@view');
      $router->post('search', 'UserController@search');
      $router->delete('delete/{id}', 'UserController@delete');

      // User Page
      $router->group(['prefix' => 'page'], function ($router) {
        $router->post('add/{page_id}', 'UserPageController@save');
        $router->put('edit/{page_id}', 'UserPageController@save');
        $router->get('exists/{page_id}', 'UserPageController@exists');
      });

    }); // e.o Users

    // Fb Pages
    $router->group(['prefix' => 'page'], function ($router) {
      $router->post('add', 'FbPageController@add');
      $router->get('browse', 'FbPageController@browse');
      $router->get('{id}/view', 'FbPageController@view');
      $router->put('{id}/edit', 'FbPageController@edit');
    }); // e.o Fb Pages

    // Message
    $router->group(['prefix' => 'message'], function($router) {
      $router->get('browse/{fb_page_id}', 'MessageController@browse');
      $router->post('add/{page_id}', 'MessageController@add');
    }); // e.o Message

  }); // e.o Middleware : Auth

  // Authentications
  $router->group(['prefix' => 'auth'], function ($router) {
    $router->post('register','AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->get('logout', 'AuthController@logout');

    // middleware : auth : only logged-ins
    $router->group(['middleware' => 'auth'], function () use ($router) {
      $router->get('me', 'AuthController@me');
      $router->get('authMe', 'AuthController@authMe');
      $router->post('refresh', 'AuthController@refresh');
    });
  }); // e.o Authentications
});