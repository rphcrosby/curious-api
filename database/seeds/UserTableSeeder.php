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
            'email' => 'rphcrosby@gmail.com',
            'role_id' => 1,
            'invite_code' => rand(100000, 999999),
            'invite_count' => config('curious.invites')
        ]);

        factory(User::class, 20)->create()->each(function($u)
        {
            $u->role_id = rand(1, 2);
            $u->save();
        });
    }
}
