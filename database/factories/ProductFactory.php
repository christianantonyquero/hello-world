<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(3, true),
        'description' => $faker->sentence,
        'price' => $faker->randomNumber(2),
        'is_published' => true,
        'shopify_variant_id' => 'Fake-Variant-ID-' . $faker->unique()->randomNumber(5),
    ];
});
