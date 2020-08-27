<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Port::class, function (Faker $faker) {
    return [
        'port' => $faker->randomNumber(4),
    ];
});
