<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Host::class, function (Faker $faker) {
    return [
        'name' => $faker->domainName,
        'ip' => $faker->ipv4,
    ];
});
