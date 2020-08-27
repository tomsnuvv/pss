<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Finding::class, function (Faker $faker) {
    return [
        'status_id' => function () {
            return App\Models\FindingStatus::inRandomOrder()->first()->id;
        },
        'severity_id' => function () {
            return App\Models\Severity::inRandomOrder()->first()->id;
        },
        'title' => $faker->text(50),
        'details' => $faker->text,
        'vulnerability_id' => function () {
            $vulnerability = App\Models\Vulnerability::inRandomOrder()->first();
            return $vulnerability ? $vulnerability->id : null;
        },
        'vulnerability_type_id' => function () {
            return App\Models\VulnerabilityType::inRandomOrder()->first()->id;
        },
        'module_id' => function () {
            return App\Models\Module::inRandomOrder()->first()->id;
        },
    ];
});
