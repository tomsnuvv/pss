<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Domain::class, function (Faker $faker) {
    return [
        'name' => $faker->domainName,
    ];
});
