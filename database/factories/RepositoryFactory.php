<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Repository::class, function (Faker $faker) {
    return [
        'name' => $faker->text(20),
        'url' => $faker->url,
        'clone_url' => $faker->url,
    ];
});
