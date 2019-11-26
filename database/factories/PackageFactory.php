<?php

/** @var Factory $factory */

use App\Package;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Package::class, function (Faker $faker) {
    return [
        'title'=> $faker-> name,
        'description'=> $faker-> text,
        'image'=> $faker-> url,
        'price'=>$faker->numberBetween(100,200)
    ];
});