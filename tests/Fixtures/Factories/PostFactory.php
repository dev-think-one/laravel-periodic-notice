<?php

namespace PeriodicNotice\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use PeriodicNotice\Tests\Fixtures\Models\Post;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'        => $this->faker->unique()->sentence(),
            'published_at' => $this->faker->unique()->dateTime(),
        ];
    }

    public function title(string $title)
    {
        return $this->state([
            'title' => $title,
        ]);
    }

    public function publishedAt(\DateTimeInterface $datetime)
    {
        return $this->state([
            'published_at' => $datetime,
        ]);
    }
}
