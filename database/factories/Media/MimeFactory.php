<?php

namespace Database\Factories\Media;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media\Mime>
 */
class MimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'path' => '/path/to/file',
            'mime_id' => 28,
            'disk_id' => 1
        ];
    }
}
