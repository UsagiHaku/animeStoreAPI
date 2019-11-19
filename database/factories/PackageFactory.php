<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Package;
use Faker\Generator as Faker;

$factory->define(Package::class, function (Faker $faker) {
    return [
        'title'=> $faker-> name,
        'description'=> $faker-> text,
        'image'=> $faker-> url,
        'price'=>$faker->numberBetween(100,200)
    ];
});
