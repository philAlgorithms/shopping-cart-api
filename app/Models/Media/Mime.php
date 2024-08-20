<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Mime extends Model
{
    use HasFactory;

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    function toBase64Url(string $string)
    {
        return "data:" . $this->type . ";base64," . base64_encode($string);
    }
}
