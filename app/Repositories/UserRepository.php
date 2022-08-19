<?php

namespace App\Repositories;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserRole;
use App\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
  public function add($data) {
    $user = new User;
    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->password = app('hash')->make($data['password']);
    $user->is_active = $data['is_active'];
    $user->is_confirmed = $data['is_confirmed'];
    $user->user_role_id = $data['user_role_id'];
    $user->save();

    // IDs -> regional admin 2 : courier 5 : township admin 6 
    /*
    $role = $user->user_role_id;
    if($role === 2 || $role === 5 || $role === 6) {
      $user->adminDetail()->create([
        'user_id' => $user->id,
        'coverage_region_id' => $data['coverage_region_id'],
        'coverage_township_id' => $role === 5 || $role === 6 ? $data['coverage_township_id'] : null,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
      ]);
    }
    */

    if($data->has('pages')) {
      foreach ($data as $page) {
        $user->pages()->create([
          'user_id'     => $user->id,
          'page_id'     => $page,
          'is_joined'   => true,
          'joined_date' => Carbon::now(),
          'created_at'  => Carbon::now(),
          'updated_at'  => Carbon::now()
        ]);
      }
    }

    return $user;
  }

  public function browse() {
    return User::get();
  }

  public function roles() {
    return UserRole::where('id', '!=', 1)->get();
  }

  public function admins() {
    return User::where('user_role_id', '=', 2)->with(['adminDetail.region'])->get();
  }

  public function users() {
    return User::where('user_role_id', '=', 3)->get();
  }

  public function view($data_id) {
    return User::find($data_id);
  }

  public function edit($id, $data) {
    $user = User::where('id', '=', $id)
              // ->with('addresses', 'addresses.region', 'addresses.township', 'addresses.ward')
              ->first();

    $user->name   = $data['name'];
    $user->phone  = $data['phone'];
    $user->email  = $data['email'];

    if(isset($data['password'])) {
      $user->password   = app('hash')->make($data['password']);
    }

    $user->updated_at = Carbon::now();
    $user->save();

    return $user;
  }

  public function updateFirebaseToken($id, $token) {
    $user = User::find($id);
    $user->firebase_token = $token;
    $user->save();
    
    return $user;
  }

  public function confirm($id) {
    $user = User::where('id', '=', $id)->first();
    $user->is_confirmed = true;
    $user->is_active = true;
    $user->updated_at = Carbon::now();
    $user->save();

    return ["message" => "User confirmed."];
  }

  public function search($data) {
    return User::where('name', 'like', '%' . $data . '%')->get();
  }

  public function delete($data_id) {
    return User::where('id', '=', $data_id)->delete();
  }

  public function exists($identifier, $identity) {
    return User::where($identifier, '=', $identity)->exists();
  }

  /*
  public function updateImage($id, $file)
  {
    $photo = Product::where('id', '=', $id)->first();
    $photo->image = $file;
    $photo->save();
    return $photo;
  }

  public function deleteImage($id)
  {
    $photo = Product::where('id', '=', $id)->get();
    $p = $photo[0]->image;
    File::delete($p);
  }
  */
}
