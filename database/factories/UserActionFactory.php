<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\Laravelayers\Auth\Models\UserAction::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(\Laravelayers\Auth\Models\User::class)->create()->id;
        },
        'action' => 'test.' . $faker->word(),
        'allowed' => 1,
        'ip' => null
    ];
});
