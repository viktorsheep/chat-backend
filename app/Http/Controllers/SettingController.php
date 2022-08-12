<?php

namespace App\Http\Controllers;

use App\Interfaces\SettingRepositoryInterface;
use Illuminate\Http\Request;
use Exception;

class SettingController extends Controller {
  protected $setting;

  public function __construct(
    SettingRepositoryInterface $setting
  )
  {
    $this->setting = $setting;
  }

  public function edit($name, Request $request) {
    try {
      return $this->successResponse(
        $this->setting->save($name, $request->value)
      );
    } catch (Exception $e) {
      $this->er500($e->getMessage());
    }
  }

  public function viewByName($name) {
    try {
      return $this->successResponse(
        $this->setting->viewByName($name)
      );
    }
    catch(Exception $e) {
      $this->er500($e->getMessage());
    }
  }
}