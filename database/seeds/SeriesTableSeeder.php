<?php

use App\Category;
use App\Serie;
use App\User;
use Illuminate\Database\Seeder;

class SeriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Serie::class,10)->create();
        foreach(Serie::all() as $serie) {
            $serie->categories()
                ->attach(Category::all()->random(2));
            $serie->users()
                ->attach(User::all()->random(1));
        }
    }
}
