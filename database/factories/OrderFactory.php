<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'total'=> $faker->numberBetween(100, 500),
        'card_id'=> $faker->title,
        'delivery_date'=>$faker->date()
    ];
});
