<?php

namespace App\Models;

use App\Models\Media\MediaFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphToMany};

class Advert extends Model
{
    use HasFactory;

    public function image(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'image_id');
    }

    /**
     * Gets the tags associated with this advert
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image->temporary_url;
    }
}
