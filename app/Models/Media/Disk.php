<?php

namespace App\Models\Media;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Disk extends Model
{
    use HasFactory;

    public function getConfigurationAttribute(): mixed
    {
        return config('filesystems.disks.' . $this->name);
    }

    public function storage(): Filesystem
    {
        return Storage::disk($this->name);
    }

    public function files(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'disk_id');
    }

    public function generateInitialLetterSvg(string $path, string $letter, bool $create_media_file=false, ?string $key=null, array $options = []): MediaFile|bool
    {
        $letter = $letter[0] ?? $letter;
        $content = <<<EOD
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
                <rect width="100%" height="100%" fill="#ddd" />
                <text x="50" y="80" font-size="85" font-family="sans-serif" text-anchor="middle" fill="black">{$letter}</text>
            </svg>
        EOD;
        $file_created = $this->storage()->put($path, $content, $options);

        if($create_media_file && $file_created)
        {
            $mime = Mime::firstWhere('extension', '.svg');
            return $this->files()->create([
                'key' => $key,
                'mime_id' => $mime->id,
                'path' => $path
            ]);
        }

        return $file_created;
    }
}
