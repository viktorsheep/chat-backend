<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageTypeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('message_types')->insert([
      'name' => 'send',
      'description' => 'sending message'
    ]);

    DB::table('message_types')->insert([
      'name' => 'receive',
      'description' => 'receiving message'
    ]);
  }
}
