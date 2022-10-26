<?php

namespace App\Interfaces;

interface PageRepositoryInterface
{
  public function save($id = null, $data);

  public function browse($with_token = false);

  public function view($id, array $fields = null);

  public function search($data);

  public function delete($id);

  public function toggleActive($id);
}
