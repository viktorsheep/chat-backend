<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('user_roles')->insert([
      'name' => 'Super Admin',
      'description' => 'for super admin'
    ]);

    DB::table('user_roles')->insert([
      'name' => 'Admin',
      'description' => 'for admin'
    ]);

    DB::table('user_roles')->insert([
      'name' => 'Client',
      'description' => 'for clients'
    ]);
  }
}
