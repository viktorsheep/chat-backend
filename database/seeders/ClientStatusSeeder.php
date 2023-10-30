<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientStatusSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('client_status')->insert([
            'name' => 'fruitful contact',
        ]);
        DB::table('client_status')->insert([
            'name' => 'maybe fruitful',
        ]);
        DB::table('client_status')->insert([
            'name' => 'not fruitful',
        ]);
    }
}
