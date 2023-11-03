<?php

use Carbon\Carbon;

// Root
$router->get('/', function () use ($router) {
    return Carbon::parse(Carbon::now())->timezone('Asia/Yangon')->toDateTimeString();
});

// File
/*
$router->group(['middleware' => 'auth'], function ($router) {
  $router->get('{file}', function ($file) {
    return response()->download($file);
  });
});
*/

// File
$router->group(['prefix' => 'file'], function () use ($router) {
    $router->get('{file}', function ($file) {
        return response()->download($file);
    });
});

// APIs

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->get('/noti/{page_id}', 'MessageController@noti');

    $router->get('test', function () use ($router) {
        return "hello";
    });

    $router->group(['prefix' => 'file'], function () use ($router) {
        $router->get('yo', function () use ($router) {
            return 'hello';
        });

        $router->post('upload', 'MessageController@uploadAudio');
        $router->delete('delete', 'MessageController@deleteAudio');
    });

    // Facebook Webhooks
    $router->group(['prefix' => 'facebook'], function ($router) {
        $router->post('webhook', 'MessageController@receiveNotification');
        $router->get('webhook', 'MessageController@verifyWebhook');
    }); // e.o Facebook Webhooks

    // Set Client
    $router->group(['prefix' => 'client'], function ($router) {
        $router->post('set', 'ClientController@set');
        $router->put('{client_mid}/{status_id}/set-status', 'ClientController@setStatus');
        $router->put('{client_psid}/{responder_id}/set-responder', 'ClientController@setResponder');
        $router->put('{client_mid}/read-message', 'ClientController@readMessage');
        $router->get('{client_mid}/getData', 'ClientController@getData');
        $router->get('{client_mid}/getClient', 'ClientController@getClient');
        $router->get('{sender_id}/{page_id}/get', 'ClientController@getBySenderId');

        $router->get('{status_id}/{responder_id}/{page_id}/get', 'ClientController@filteredData');
    }); // e.o Set Client

    // Client Status
    $router->group(['prefix' => 'client-statuses'], function ($router) {
        $router->get('all', 'ClientStatusController@getClientStatus');
        $router->get('{id}/get', 'ClientStatusController@getById');
    });

    // Middleware : Auth
    $router->group(['middleware' => 'auth'], function () use ($router) {

        // Users
        $router->group(['prefix' => 'user'], function ($router) {
            $router->post('add', 'UserController@add');
            // $router->get('browse', 'UserController@browse');
            // $router->get('admins', 'UserController@admins');
            $router->get('users', 'UserController@users');
            $router->get('roles', 'UserController@roles');
            $router->put('edit/{id}', 'UserController@edit');
            $router->get('view/{id}', 'UserController@view');
            $router->post('search', 'UserController@search');
            $router->delete('delete/{id}', 'UserController@delete');

            $router->group(['prefix' => '{id}'], function ($router) {
                $router->put('update/firebase_token', 'UserController@updateFirebaseToken');
            });

            // User Page
            $router->group(['prefix' => 'page'], function ($router) {
                $router->post('add/{page_id}[/{user_id}]', 'UserPageController@save');
                $router->put('edit/{page_id}[/{user_id}]', 'UserPageController@save');
                $router->get('get/{user_id}', 'UserPageController@get');
                $router->get('exists/{page_id}', 'UserPageController@exists');
                $router->delete('delete/{id}', 'UserPageController@delete');
            });
        }); // e.o Users

        // Fb Pages
        $router->group(['prefix' => 'page'], function ($router) {
            $router->post('add', 'FbPageController@add');
            $router->get('browse', 'FbPageController@browse');

            $router->group(['prefix' => '{id}'], function ($router) {
                $router->get('view', 'FbPageController@view');
                $router->put('edit', 'FbPageController@edit');

                // access_token
                $router->group(['prefix' => 'access_token'], function ($router) {
                    $router->get('get', 'FbPageController@getAccessToken');
                    $router->put('update', 'FbPageController@updateAccessToken');
                });
            });
        }); // e.o Fb Pages

        // Message
        $router->group(['prefix' => 'message'], function ($router) {
            $router->get('browse/{fb_page_id}', 'MessageController@browse');
            $router->post('get-audio', 'MessageController@getAudio');
            $router->post('add/{page_id}', 'MessageController@add');
        }); // e.o Message

        // Setting
        $router->group(['prefix' => 'setting'], function ($router) {
            $router->get('{name}/view', 'SettingController@viewByName');
            $router->put('{name}/edit', 'SettingController@edit');
        }); // e.o Setting

        // Facebook Page api
        $router->group(['prefix' => '{page_id}'], function ($router) {
            $router->group(['prefix' => '{conversation_id}'], function ($router) {
                $router->get('attachments', 'FacebookController@attachments');
                $router->get('messages', 'FacebookController@messages');
                $router->get('conversation', 'FacebookController@conversation');
            });
            $router->get('/', 'FacebookController@profile');
            $router->get('conversations', 'FacebookController@conversations');
        }); // e.o Facebook Page apis

        // Facebook Me api
        $router->group(['prefix' => 'me'], function ($router) {
            $router->post('/send-messages', 'FacebookController@sendMessage');
            $router->post('/send-voice', 'FacebookController@sendVoice');
        }); // e.o Facebook Me api

    }); // e.o Middleware : Auth

    // Authentications
    $router->group(['prefix' => 'auth'], function ($router) {
        $router->post('register', 'AuthController@register');
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
