<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Идемпотентность: если объявления уже есть — считаем, что сидер уже отработал,
        // и не задваиваем данные при повторном запуске.
        if (Ad::count() > 0) {
            return;
        }

        $categoryNames = ['Электроника', 'Недвижимость', 'Транспорт'];

        $categories = collect($categoryNames)->map(
            fn (string $name) => Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            )
        );

        $userDefinitions = [
            ['name' => 'Admin', 'email' => 'admin@boardy.local', 'role' => 'admin'],
            ['name' => 'Ivan Petrov', 'email' => 'ivan@boardy.local', 'role' => 'user'],
            ['name' => 'Olga Sidorova', 'email' => 'olga@boardy.local', 'role' => 'user'],
        ];

        $users = collect($userDefinitions)->map(
            fn (array $attrs) => User::firstOrCreate(
                ['email' => $attrs['email']],
                [
                    'name' => $attrs['name'],
                    'role' => $attrs['role'],
                    'password' => Hash::make('password'),
                ]
            )
        );

        $faker = fake();

        for ($i = 0; $i < 50; $i++) {
            Ad::create([
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'title' => rtrim($faker->sentence(4), '.'),
                'description' => $faker->paragraph(),
                'price' => $faker->randomFloat(2, 10, 5000),
                'status' => 'active',
            ]);
        }
    }
}
