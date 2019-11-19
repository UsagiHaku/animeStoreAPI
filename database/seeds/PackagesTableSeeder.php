<?php

use App\Package;
use App\Serie;
use Illuminate\Database\Seeder;

class PackagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Package::class,2)->create();
        foreach(Package::all() as $package) {
            $package->series()
                ->attach(Serie::all()->random(1)->first());
        }
    }
}
