<?php

namespace App\Interfaces;

interface UserPageRepositoryInterface {

  public function save($page_id, $user_id = null);

  public function get($user_id);

  public function exists($page_id);

  public function delete($id);
}