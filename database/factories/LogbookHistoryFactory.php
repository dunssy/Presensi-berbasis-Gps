<?php

namespace Database\Factories;

use App\Models\Logbook;
use App\Models\LogbookHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LogbookHistory>
 */
class LogbookHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'logbook_id' => Logbook::factory(),
            'user_id' => User::factory(),
            'action' => 'created',
            'note' => 'Logbook entry initial creation',
        ];
    }
}
