<?php

namespace App\Interfaces;

interface UserPageRepositoryInterface {

  public function save($page_id);

  public function get($user_id);

  public function exists($page_id);

}