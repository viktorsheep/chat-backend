<?php

namespace App\Interfaces;

interface SettingRepositoryInterface
{
  public function save($name = null, $value);

  public function browse();

  public function view($id);

  public function viewByName($name);

  public function search($data);

  public function delete($id);
}
