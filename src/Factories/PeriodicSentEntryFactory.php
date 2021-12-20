<?php

namespace PeriodicNotice\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use PeriodicNotice\Models\PeriodicSentEntry;

class PeriodicSentEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PeriodicSentEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'group'   => 'default',
            'sent_at' => $this->faker->dateTime(),
        ];
    }

    public function group(string $group = 'default')
    {
        return $this->state([
            'group' => $group,
        ]);
    }
}
