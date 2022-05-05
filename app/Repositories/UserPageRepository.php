<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\UserPage;
use App\Interfaces\UserPageRepositoryInterface;

class UserPageRepository implements UserPageRepositoryInterface {
  public function save($page_id) {
    $page = UserPage::firstOrNew([
      'page_id' => $page_id,
      'user_id' => auth()->user()->id
    ]);

    if($page->exists) {

      if($page->is_joined) {
        $page->is_joined = false;
        $page->left_date = Carbon::now();
      } else {
        $page->is_joined = true;
      }

      $page->updated_at = Carbon::now();

    } else {
      $page->user_id      = auth()->user()->id;
      $page->page_id      = $page_id;
      $page->is_joined    = true;
      $page->joined_date  = Carbon::now();
      $page->created_at   = Carbon::now();
      $page->updated_at   = Carbon::now();
    }

    $page->save();

    return $page;
  }

  public function exists($page_id) {
    $exists = UserPage
      ::where('user_id', '=', auth()->user()->id)
      ->where('page_id', '=', $page_id)
      ->exists();

    if($exists) {
      $up = UserPage
        ::where('user_id', '=', auth()->user()->id)
        ->where('page_id', '=', $page_id)
        ->pluck('is_joined')
        ->first();

      $up
        ? $response = "Exists."
        : $response = "Left.";
    } else {
      $response = "Does not exists.";
    }
  
    return $response;
  }
}