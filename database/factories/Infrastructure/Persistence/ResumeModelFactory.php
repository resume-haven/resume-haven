<?php

declare(strict_types=1);

namespace Database\Factories\Infrastructure\Persistence;

use App\Infrastructure\Persistence\ResumeModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Infrastructure\Persistence\ResumeModel>
 */
final class ResumeModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string
     */
    protected $model = ResumeModel::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'status' => 'draft',
        ];
    }
}
