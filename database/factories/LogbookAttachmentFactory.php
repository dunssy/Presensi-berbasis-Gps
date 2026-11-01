<?php

namespace Database\Factories;

use App\Models\Logbook;
use App\Models\LogbookAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LogbookAttachment>
 */
class LogbookAttachmentFactory extends Factory
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
            'file_path' => 'attachments/sample.pdf',
            'file_name' => 'sample.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 1024,
        ];
    }
}
