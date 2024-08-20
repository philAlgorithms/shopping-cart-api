<?php

namespace App\Models\Media;

use App\Models\Tag;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphToMany};
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class MediaFile extends Model
{
    use HasFactory;

    /**
     * Gets the disk where this file is stored in
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function disk(): BelongsTo
    {
        return $this->belongsTo(Disk::class);
    }

    /**
     * Gets the mime details of this file
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mime(): BelongsTo
    {
        return $this->belongsTo(Mime::class);
    }

    /**
     * Gets the tags associated with this file
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getUrlAttribute()
    {
        return $this->disk->storage()->url($this->path);
    }

    public function temporaryUrl(?int $minutes=5)
    {
        if(in_array($this->disk->name, ['s3', 'google']))
        {
            return $this->disk->storage()->temporaryUrl($this->path, $minutes);
        }
        if(in_array($this->disk->name, ['public']))
        {
            return $this->disk->storage()->url($this->path);
        }
        return URL::temporarySignedRoute(
            'file.temp',
            now()->addMinutes($minutes),
            [
                'disk' => $this->disk->name,
                'path' => Crypt::encryptString($this->path)
            ]
        );
    }

    public function getTemporaryUrlAttribute()
    {
        return $this->temporaryUrl();
    }

    public function getAbsolutePathAttribute()
    {
        return $this->disk->storage()->path($this->path);
    }

    public function remove(bool $delete_file = true)
    {
        if ($delete_file) {
            $this->deleteFile();
        }

        $this->delete();
    }

    public function deleteFile()
    {
        try {
            $this->disk->storage()->delete($this->path);
        } catch (Exception $e) {
            throw ValidationException::withMessages(['Unable to remove source file. Action aborted']);
        }
    }

    function toBase64Url()
    {
        return $this->mime->toBase64Url(file_get_contents($this->absolute_path));
    }

    public function getIsMultimediaAttribute(): bool
    {
        return in_array(stringBefore($this->mime->type, '/'), ['image', 'video']);
    }
}
