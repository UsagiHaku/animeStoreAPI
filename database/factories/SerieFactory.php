<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Serie;
use Faker\Generator as Faker;

// Cre instancias de un modelo , en esta caso instancias para el modelo serie



$factory->define(Serie::class, function (Faker $faker) {
    return [
        'name'=> $faker-> name,
        'description'=> $faker-> text,
        'image'=> $faker-> url
    ];
});
