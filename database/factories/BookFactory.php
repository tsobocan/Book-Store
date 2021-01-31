<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => ucfirst(implode(' ', $this->faker->words(rand(3,5)))),
            'year' => $this->faker->year,
            'created_at' => now(),
            'quantity' => rand(1,5)
        ];
    }
}
