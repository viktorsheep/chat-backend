<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\UserPage;
use App\Interfaces\UserPageRepositoryInterface;
use Exception;

class UserPageRepository implements UserPageRepositoryInterface {
  public function save($page_id, $user_id = null) {

    $uid = $user_id === null ? auth()->user()->id : $user_id;

    $page = UserPage::firstOrNew([
      'page_id' => $page_id,
      'user_id' => $uid
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
      $page->user_id      = $uid;
      $page->page_id      = $page_id;
      $page->is_joined    = true;
      $page->joined_date  = Carbon::now();
      $page->created_at   = Carbon::now();
      $page->updated_at   = Carbon::now();
    }

    $page->save();

    return $page;
  }

  public function get($user_id) {
    return UserPage::where('user_id', '=', $user_id)->get();
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

  public function delete($id) {
    $flag = true;
    try {
      UserPage::where('id', '=', $id)->delete();
    } catch (Exception $e) {
      $flag = [
        'success' => false,
        'ex' => $e->getMessage()
      ];
    }

    return $flag;
  }
}