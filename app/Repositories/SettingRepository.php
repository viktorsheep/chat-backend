<?php

namespace App\Repositories;
use Carbon\Carbon;
use App\Interfaces\SettingRepositoryInterface;
use App\Models\Setting;
use Exception;

class SettingRepository implements SettingRepositoryInterface {

  public function save($name = null, $value) {

    try {
      $setting = $name === null
        ? new Setting
        : Setting::where('name', '=', $name)->first();

      $setting->name = $name;
      $setting->value = $value;
      
      if(is_null($name)) {
        $setting->created_at = Carbon::now();
      }

      $setting->updated_at = Carbon::now();
      $setting->save();

      return $setting;
    } catch(Exception $e) {
      return $e;
    }
  }

  public function browse() {
    // TODO : Make pagination
    return Setting::get();
  }

  public function view($id) {
    return Setting::find($id);
  }

  public function viewByName($name)
  {
    try {
      return Setting::where('name', '=', $name)->first();
    } catch(Exception $e) {
      return $e;
    }
  }

  public function search($data) {
    return Setting::where('name', 'like', '%' . $data . '%')->get();
  }

  public function delete($data_id) {
    return Setting::where('id', '=', $data_id)->forceDelete();
  }
}
