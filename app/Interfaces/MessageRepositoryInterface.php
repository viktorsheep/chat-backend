<?php

namespace App\Interfaces;

interface MessageRepositoryInterface
{
  public function save($id = null, $data);

  public function browse($user_id, $fb_page_id);

  public function view($id);

  public function search($data);

  public function delete($id);
}
