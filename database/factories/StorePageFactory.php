<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StorePage;
use Faker\Generator as Faker;

$factory->define(StorePage::class, function (Faker $faker) {
    return [
        'name' => 'Page - ' . $faker->sentence(4),
        'slug' => 'page-' . $faker->unique()->slug,
        'background_image' => 'store_pages/background_images/fake-bg.jpg'
    ];
});