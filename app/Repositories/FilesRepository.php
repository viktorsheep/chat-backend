<?php

namespace App\Repositories;

use App\Interfaces\FilesRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\File;

class FilesRepository implements FilesRepositoryInterface
{

  // Save
  public function save($request)
  {
    $file = $request->file('file');
    $ext = $file->getClientOriginalExtension();
    $name = $request->file_prefix . uniqid() . '.' . $ext;
    $path = '';
    foreach ($request->file_path as $fp) {
      $path = $path . $fp . DIRECTORY_SEPARATOR;
    }

    $request->file('file')->move($path, $name);
    return $path . $name;
  }

  // Delete
  public function delete($file_path)
  {
    $result = true;
    $file = public_path($file_path);
    try {
      File::delete($file);
    } catch (Exception $e) {
      $result = $e;
    }
    return $result;
  }
}
