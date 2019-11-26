<?php

use App\Serie;
use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class,2)->create();
        foreach (User::all() as $user) {
            $user->series()
                ->attach(Serie::all()->random(2));

        }
    }
}
