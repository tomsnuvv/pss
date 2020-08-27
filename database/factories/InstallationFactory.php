<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Installation::class, function (Faker $faker) {
    return [
        'product_id' => function () {
            return App\Models\Product::inRandomOrder()->first()->id;
        },
        'module_id' => function () {
            return App\Models\Module::inRandomOrder()->first()->id;
        },
        'version' => $faker->randomNumber(3),
    ];
});
