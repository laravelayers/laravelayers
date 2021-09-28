<?php

namespace Database\Factories\Laravelayers\Auth\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravelayers\Auth\Models\UserAction;

class UserActionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \Laravelayers\Auth\Models\User::factory(),
            'action' => 'test.' . $this->faker->word() . '.' . $this->faker->word(),
            'allowed' => 1,
            'ip' => null
        ];
    }
}
