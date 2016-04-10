<?php

use Illuminate\Database\Seeder;

class ClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->insert([
            'id' => env('API_ID'),
            'secret' => env('API_SECRET'),
            'name' => 'mobile'
        ]);
    }
}
