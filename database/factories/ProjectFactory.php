<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'title'       => $this->faker->sentence(4, true),
            'description' => $this->faker->paragraph(),
            'category'    => $this->faker->randomElement(['Agentic AI', 'Web Development', 'Research', 'Data Science']),
            'status'      => $this->faker->randomElement(['draft', 'active', 'archived']),
            'tech_stack'  => implode(', ', $this->faker->randomElements(
                ['Python', 'Laravel', 'LangGraph', 'PostgreSQL', 'Gemini', 'FastAPI', 'Vue.js', 'React'],
                $this->faker->numberBetween(2, 4)
            )),
            'started_at'  => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
