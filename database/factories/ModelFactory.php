<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function ($faker) {
    return [
        'username' => $faker->userName,
        'email' => $faker->email,
        'password' => 'password',
        'invite_code' => $faker->numberBetween(100000, 999999),
        'invite_count' => config('curious.invites')
    ];
});

$factory->defineAs(App\User::class, 'admin', function ($faker) {
    return [
        'username' => $faker->userName,
        'email' => $faker->email,
        'password' => 'password',
        'invite_code' => $faker->numberBetween(100000, 999999),
        'invite_count' => config('curious.invites'),
        'role_id' => 1
    ];
});

$factory->defineAs(App\Role::class, 'admin', function ($faker) {
    return [
        'name' => 'administrator'
    ];
});

$factory->define(App\Tag::class, function ($faker) {
    return [
        'name' => str_random(4)
    ];
});

