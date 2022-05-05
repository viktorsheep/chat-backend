<?php

namespace App\Interfaces;

interface PageRepositoryInterface
{
  public function save($id = null, $data);

  public function browse();

  public function view($id);

  public function search($data);

  public function delete($id);

  public function toggleActive($id);
}
