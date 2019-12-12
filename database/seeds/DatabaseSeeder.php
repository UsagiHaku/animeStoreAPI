<?php

use App\Category;
use App\Comment;
use App\Order;
use App\Package;
use App\Serie;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class,2)->create();
        factory(User::class)->create([
            "email" => "sheilaricalde@gmail.com",
            "password" => bcrypt("passw0rd")
        ]);
        $swagerUser =factory(User::class)->create([
            "email" => "john@email.com",
            "password" => bcrypt("thiswillbeencrypted")
        ]);

        factory(Serie::class,10)->create();
        factory(Package::class,3)->create();
        factory(Category::class,10)->create();
        factory(Comment::class,30)->create([
            'user_id' => User::all()->random(1)->first(),
            'serie_id' => Serie::all()->random(1)->first()
        ]);
        factory(Order::class, 3)->create([
            'user_id' => User::all()->random(1)->first(),
        ]);

        factory(Order::class)->create([
            'user_id' => User::find($swagerUser->id)->id,
        ]);

        foreach (User::all() as $user) {
            $user->series()
                ->attach(Serie::all()->random(2));

        }

        foreach(Serie::all() as $serie) {
            $serie->categories()
                ->attach(Category::all()->random(2));
            $serie->users()
                ->attach(User::all()->random(1));
        }

        foreach(Package::all() as $package) {
            $package->series()
                ->attach(Serie::all()->random(1)->first());
        }

        foreach(Category::all() as $category) {
            $category->series()
                ->attach(Serie::all()->random(1)->first());
        }
    }
}
