<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Game;
use Faker\Generator as Faker;

$factory->define(Game::class, function (Faker $faker) {
    return [
        'userId' => 1,
        'name' => $faker->name,
        'platformType' => 'Console',
        'format' => 'Physical',
        'owned' => 1,
        'wishlist' => 0,
        'backlog' => 0,
    ];
});
