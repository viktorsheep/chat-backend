<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('users')->insert([
      'name'          => 'Jill Hamza',
      'email'         => 'jill@demo.com',
      'password'      => Hash::make('demo'),
      'is_active'     => 1,
      'is_confirmed'  => 1,
      'created_at'    => Carbon::now(),
      'updated_at'    => Carbon::now(),
      'user_role_id'  => 1,
      'firebase_token'=> ''
    ]);
  }
}
