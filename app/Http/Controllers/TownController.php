<?php

namespace App\Http\Controllers;

use App\Models\Location\Town;
use App\Http\Requests\Validators\TownValidator;
use App\Http\Resources\Location\TownResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TownController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $state_id = request('state_id');
        $towns = Town::query()
            ->when(request('tags') && is_array(request('tags')),
                fn($builder) => $builder->whereHas(
                    'tags',
                    fn ($builder) => $builder->whereIn('tag_id', request('tags'))
                )->with('tags')
            )
            ->when(request('states') && is_array(request('states')),
                fn($builder) => $builder->whereHas(
                    'states',
                    fn($builder) => $builder->whereIn('state_id', request('states'))
                )->with('states')
            )
            ->when(
                $state_id && (is_array($state_id) || is_numeric($state_id)),
                fn ($builder) => $builder->whereIn('state_id', is_array($state_id) ? $state_id : [$state_id])
            )
            ->paginate(getpaginator(request()));

        return TownResource::collection(
            $towns
        );
    }

    /**
     * Create a new town resource
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function create()
    {
        $this->authorize('create', [Town::class]);
        
        $validated = (new TownValidator)->validate(
            $town = new Town(),
            request()
        );

        $town = DB::transaction(function () use (
            $town, $validated
        ) {
            $prepared = Arr::except($validated, ['tags']);

            $town->fill($prepared);
            $town->save();

            $town->tags()->attach($validated['tags'] ?? []);

            return $town;
        });

        return TownResource::make(
            $town->load(['brand', 'store', 'coverImage'])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Skills\Skill  $skill
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show(Town $town)
    {
        $town
            ->load(['state']);

        return TownResource::make($town);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Location\Town  $town
     * @return \Illuminate\Http\Response
     */
    public function update(Town $town)
    {
        $this->authorize('create', [Town::class]);
        $this->authorize('update', $town);
        
        $validated = (new TownValidator)->validate($town, request());

        $town = DB::transaction(function () use ($town, $validated) {
            $prepared = Arr::except($validated, ['tags']);

            $town->fill($prepared);
            $town->save();
            
            $town->tags()->attach($validated['tags'] ?? []);

            return $town;
        });

        return TownResource::make(
            $town
        );
    }

    /**
     * Remove the specified resource from public view.
     *
     * @param  \App\Models\Location\Town  $skill
     */
    public function delete(Town $town)
    {
        $this->authorize('delete', $town);
        $town->delete();
    }

    /**
     * Remove the town entirely.
     *
     * @param  \App\Models\Location\Town  $town
     */
    public function forceDelete(Town $town)
    {
        $this->authorize('forceDelete', $town);
        $town->forceDelete();
    }
}
