<?php

    namespace Database\Factories;

    use App\Models\User;
    use Ramsey\Uuid\Uuid;
    use App\Models\BookOption;
    use App\Models\Models\Book;
    use App\Models\Models\Author;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Str;

    class BookOptionFactory extends Factory
    {
        /**
         * The name of the factory's corresponding model.
         *
         * @var string
         */
        protected $model = BookOption::class;

        /**
         * Define the model's default state.
         *
         * @return array
         */
        public function definition()
        {
            return [
                'title' => Uuid::uuid4()->toString(),
            ];
        }

    }
