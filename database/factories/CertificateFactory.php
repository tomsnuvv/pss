<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Certificate::class, function (Faker $faker) {
    return [
        'name' => $faker->text,
        'creation_date' => $faker->dateTime,
        'expiration_date' => $faker->dateTime
    ];
});
