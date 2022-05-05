<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {

    // User
    $this->app->bind(
      'App\Interfaces\UserRepositoryInterface',
      'App\Repositories\UserRepository'
    );

    // Auth
    $this->app->bind(
      'App\Interfaces\AuthRepositoryInterface',
      'App\Repositories\AuthRepository'
    );

    // Files
    $this->app->bind(
      'App\Interfaces\FilesRepositoryInterface',
      'App\Repositories\FilesRepository'
    );

    // Pages
    $this->app->bind(
      'App\Interfaces\PageRepositoryInterface',
      'App\Repositories\PageRepository'
    );

    // User Pages
    $this->app->bind(
      'App\Interfaces\UserPageRepositoryInterface',
      'App\Repositories\UserPageRepository'
    );

    // Messages
    $this->app->bind(
      'App\Interfaces\MessageRepositoryInterface',
      'App\Repositories\MessageRepository'
    );
  }
}
