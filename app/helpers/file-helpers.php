<?php

use App\Models\Media\{Disk, MediaFile, Mime};
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\callback;

/**
 * Organises an uploaded file's info and mime
 * 
 * @param Illuminate\Http\UploadedFile $uploaded_file An uploaded file
 * @param App\Models\Media\Disk|string $disk The preferred storage disk for the uploaded file
 * @param string $path The directory path to save the uploaded file in disk
 * 
 * @return array An array that can be used to create an \App\Models\Media\MediaFile instance
 */
function getMediaAttributes(UploadedFile $uploaded_file, Disk|string $disk, string $path = ''): array
{
    if (!($disk instanceof Disk)) {
        $working_disk = Disk::firstWhere('name', $disk);
        if (is_null($working_disk)) {
            $working_disk = Disk::firstWhere('name', env('DEFAULT_DISK', 'local'));
        }
    }

    $mime_type = $uploaded_file->getMimeType();
    $mime = Mime::firstWhere('type', $mime_type);

    $path = App::runningUnitTests() ? $path . '/test' : $path;

    return [
        'path' => $path,
        'mime_id' => $mime->id,
        'disk_id' => $working_disk->id
    ];
}

/**
 * Saves an uploaded file in storage and returns a new MediaFile instance
 * 
 * @param Illuminate\Http\UploadedFile $uploaded_file An uploaded file
 * @param App\Models\Media\Disk|string $disk The preferred storage disk for the uploaded file
 * @param string $path The directory path to save the uploaded file in disk
 * 
 * @return \App\Models\Media\MediaFile
 */
function fillMediaFile(UploadedFile $uploaded_file, Disk|string $disk, string $path = ''): MediaFile
{
    $attributes = getMediaAttributes($uploaded_file, $disk, $path);

    // $file = Storage::disk($disk->name ?? $disk)->put($path, $uploaded_file);
    $file = $uploaded_file->store($attributes['path'], $disk->name ?? $disk);
    $attributes['path'] = $attributes['path'] . '/' . pathinfo($file)['basename'];

    return (new MediaFile)->fill($attributes);
}

/**
 * Saves an uploaded file in storage and returns a newly created MediaFile
 * 
 * @param Illuminate\Http\UploadedFile $uploaded_file An uploaded file
 * @param \App\Models\Media\Disk|string $disk The preferred storage disk for the uploaded file
 * @param string $path The directory path to save the uploaded file in disk
 * 
 * @return \App\Models\Media\MediaFile
 */
function saveMediaFile(UploadedFile $uploaded_file, Disk|string $disk, string $path = ''): MediaFile
{
    $saved_file = fillMediaFile(
        uploaded_file: $uploaded_file,
        disk: $disk,
        path: $path
    );

    $saved_file->save();
    return $saved_file;
}

/**
 * Fills an existing media file with an uploaded file in storage and returns the old MediaFile instance
 * 
 * @param App\Models\Media\MediaFile $media_file
 * @param Illuminate\Http\UploadedFile $uploaded_file An uploaded file
 * @param App\Models\Media\Disk|string $disk The preferred storage disk for the uploaded file
 * @param string $path The directory path to save the uploaded file in disk
 * 
 * @return \App\Models\Media\MediaFile
 */
function editMediaFile(MediaFile $media_file, UploadedFile $uploaded_file, Disk|string|null $disk = null, string|null $path = null): MediaFile
{
    $disk = is_null($disk) ? $media_file->disk : $disk;
    $path = is_null($path) ? dirname($media_file->path) : $path;

    $attributes = getMediaAttributes($uploaded_file, $disk, $path);

    $file = $uploaded_file->store($attributes['path'], $disk->name ?? $disk);
    $attributes['path'] = $attributes['path'] . '/' . pathinfo($file)['basename'];

    return $media_file->fill($attributes);
}

/**
 * Updates an existing media file with an uploaded file in storage and returns the old MediaFile instance
 * 
 * @param \App\Models\Media\MediaFile $media_file
 * @param Illuminate\Http\UploadedFile $uploaded_file An uploaded file
 * @param \App\Models\Media\Disk|string $disk The preferred storage disk for the uploaded file
 * @param string $path The directory path to save the uploaded file in disk
 * 
 * @return \App\Models\Media\MediaFile
 */
function updateMediaFile(MediaFile $media_file, UploadedFile $uploaded_file, Disk|string|null $disk = null, string|null $path = null, ?bool $delete_media = true): MediaFile
{
    $updated_file = editMediaFile(
        media_file: $media_file,
        uploaded_file: $uploaded_file,
        disk: $disk,
        path: $path
    );

    if ($delete_media) {
        try {
            $media_file->deleteFile();
        } catch (QueryException $e) {
            // 
        }
    }
    $updated_file->save();
    return $updated_file;
}

function fillOrEditMediaFile(UploadedFile $uploaded_file, ?MediaFile $media_file = null, Disk|string|null $disk = null, string|null $path = null, ?bool $delete_media = false, ?Closure $callback = null): MediaFile
{
    // $callback = is_null($callback) ? function() {} : $callback;

    if (is_null($media_file) or $media_file->tags->contains('key', 'system')) {

        $model = fillMediaFile(uploaded_file: $uploaded_file, disk: $disk, path: $path);

        $callback($model);

        return $model;
    } else {
        $old_disk = $media_file->disk;
        $old_path = $media_file->path;
        $model = editMediaFile(
            media_file: $media_file,
            uploaded_file: $uploaded_file,
            disk: $disk,
            path: $path
        );

        if ($delete_media)
            $old_disk->storage()->delete($old_path);
        $callback($model);
        return $model;
    }
}

function saveOrUpdateMediaFile(UploadedFile $uploaded_file, ?MediaFile $media_file = null, Disk|string|null $disk = null, string|null $path = null, ?bool $delete_media = true, ?Closure $callback = null): MediaFile
{
    return fillOrEditMediaFile(
        media_file: $media_file,
        uploaded_file: $uploaded_file,
        disk: $disk,
        path: $path,
        delete_media: $delete_media,
        callback: function ($model) use ($callback) {
            $model->save();
            $callback($model);
        }
    );
}

// Concerning file generation

/**
 * Convert a file string to base64 url
 * 
 * @param string $string
 * @param string $extension The file type or the extension of the file.
 * 
 * @return string
 */
function toBase64Url(string $string, string $extension)
{
    $type = Mime::where('type', $extension)
        ->orWhere('extension', $extension)
        ->orWhere('extrension', substr($extension, 1))
        ->first()->type ?? $extension;
    return "data:" . $type . ";base64," . base64_encode($string);
}

function generateSvgFromInitial(string $string): string
{
    $letter = mb_substr($string, 0, 1);
    return <<<EOD
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
                <rect width="100%" height="100%" fill="#ddd" />
                <text x="50" y="80" font-size="85" font-family="sans-serif" text-anchor="middle" fill="black">{$letter}</text>
            </svg>
        EOD;
}

function generateSvgUrlFromInitial(string $string): string
{
    $svgString = generateSvgFromInitial($string);
    $mime = Mime::firstWhere('extension', '.svg');

    return is_null($mime) ? toBase64Url($svgString, 'image/svg+xml') : $mime->toBase64Url($svgString);
}


function specPivots($spec)
{
    return [
        'id' => $spec,
        'detail' => fake()->text(50)
    ];
}