<?php

use App\Category;
use App\Serie;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Category::class,10)->create();
        foreach(Category::all() as $category) {
            $category->series()
                ->attach(Serie::all()->random(1)->first());
        }
    }
}
