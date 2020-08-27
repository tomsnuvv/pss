<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Product::class, function (Faker $faker) {
    return [
        'type_id' => function () {
            return App\Models\ProductType::inRandomOrder()->first()->id;
        },
        'license_id' => function () {
            return App\Models\ProductLicense::inRandomOrder()->first()->id;
        },
        'vendor_id' => function () {
            return factory(App\Models\Vendor::class)->create()->id;
        },
        'name' => $faker->text(20),
        'code' => $faker->word,
        'description' => $faker->text,
        'website' => $faker->url,
        'source' => $faker->url,
        'latest_version' => $faker->randomDigit,
        'latest_info_check' => $faker->dateTime
    ];
});
