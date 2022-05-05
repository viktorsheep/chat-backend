<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
  // public function add(array $data, $file);

  public function add($data);

  public function browse();

  public function roles();

  public function admins();

  public function users();

  public function view($data_id);

  public function edit($id, $data);

  public function search($data);

  public function confirm($id);

  public function delete($data_id);

  public function exists($identifier, $identity);
  
  /*
  public function updateImage($id, $file);
  
  public function deleteImage($id);
  */
}
