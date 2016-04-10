<?php

use Illuminate\Database\Seeder;

use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'rphcrosby',
            'password' => 'password',
            'role_id' => 1
        ]);

        factory(User::class, 20)->create()->each(function($u)
        {
            $u->role_id = rand(1, 2);
            $u->save();
        });
    }
}
