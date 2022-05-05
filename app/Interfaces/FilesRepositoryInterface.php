<?php

namespace App\Interfaces;

interface FilesRepositoryInterface
{
  public function save($request);
  public function delete($request);
}
