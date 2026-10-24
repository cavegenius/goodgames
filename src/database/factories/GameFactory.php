<?php

namespace Database\Factories;

use App\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'userId' => 1,
            'name' => $this->faker->name(),
            'platform' => 'PlayStation 4',
            'platformType' => 'Console',
            'format' => 'Physical',
        ];
    }
}
