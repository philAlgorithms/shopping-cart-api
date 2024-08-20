<?php

namespace Database\Factories\Media;

use App\Models\Media\Mime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media\MediaFile>
 */
class MediaFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'path' => fake()->unique()->filePath(),
            'mime_id' => 28,
            'disk_id' => 1
        ];
    }

    public function image()
    {
        return $this->state(function (array $attributes) {
            $image_extensions = ['.jpg', '.png', '.svg', '.jpeg'];
            $image_mimes = Mime::wherein('extension', $image_extensions);
            return [
                'mime_id' => fake()->randomElement($image_mimes->pluck('id')->toArray())
            ];
        });
    }
}
