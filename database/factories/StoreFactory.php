<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Store;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Store::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'slug' => $faker->slug,
    ];
});

$factory->state(Store::class, 'test-mode', [
    'is_test_mode' => true
]);
