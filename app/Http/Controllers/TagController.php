<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Requests\Validators\TagValidator;
use App\Http\Resources\TagResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $tags = Tag::query()
                ->when(request('id'),
                    fn($builder) => $builder->wherein('id', request('id'))
                )
                ->when(request('names'),
                    fn($builder) => $builder->wherein('name', request('names'))
                )
                ->paginate(getpaginator(request()));

        return TagResource::collection(
            $tags
        );
    }

    /**
     * Create a new tag
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function create()
    {
        $this->authorize('create', [Tag::class]);
        
        $validated = (new TagValidator)->validate($tag = new Tag(), request()->all());
        $prepared = Arr::only($validated, ['name']);

        $tag->fill($prepared);
        $tag = DB::transaction(function () use ($tag) {
            $tag->save();

            return $tag;
        });

        return TagResource::make(
            $tag
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Tag $tag): JsonResource
    {
        $this->authorize('view', $tag);

        return TagResource::make(
            $tag
        );
    }

    /**
     * Update a tag
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function update(Tag $tag): JsonResource
    {
        $this->authorize('create', [Tag::class]);
        
        $validated = (new TagValidator)->validate($tag, request()->all());
        $prepared = Arr::only($validated, ['name']);

        $tag->fill($prepared);
        $tag = DB::transaction(function () use ($tag) {
            $tag->save();

            return $tag;
        });

        return TagResource::make(
            $tag
        );
    }

    /**
     * Remove the tag.
     *
     * @param  \App\Models\Tag  $tag
     */
    public function delete(Tag $tag)
    {
        $this->authorize('delete', $tag);
        $tag->delete();
    }

    /**
     * Remove the tag entirely.
     *
     * @param  \App\Models\Tag  $tag
     */
    // public function forceDelete(Tag $tag) 
    // {
            // Should tags be soft deleted?
    //     $this->authorize('forceDelete', $tag);

    //     $tag->forceDelete();
    // }
}
