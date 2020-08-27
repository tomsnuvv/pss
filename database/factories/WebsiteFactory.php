<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Website::class, function (Faker $faker) {
    return [
        'url' => 'https://endouble.com/',
    ];
});
