<?php

namespace App\Http\Controllers;

use App\Models\Specifications\Specification;
use App\Http\Requests\StoreSpecificationRequest;
use App\Http\Requests\UpdateSpecificationRequest;
use App\Http\Requests\Validators\SpecificationValidator;
use App\Http\Resources\Specifications\SpecificationResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class SpecificationController extends Controller
{
    /**
     * Display a listing of specifications.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $specifications = Specification::query()
                ->when(request('id'),
                    fn($builder) => $builder->wherein('id', request('id'))
                )
                ->when(request('names'),
                    fn($builder) => $builder->wherein('name', request('names'))
                )
                ->when(request('products'),
                    fn($builder) => $builder->whereHas('products', function ($builder2) 
                    {
                        $builder2->wherein('product_id', request('products'));
                    })
                )
                ->paginate(getpaginator(request()));

        return SpecificationResource::collection(
            $specifications
        );
    }

    /**
     * Creates a new specification
     *
     * @return \Illuminate\Http\Response
     */
    public function create():JsonResource
    {
        $this->authorize('create', Specification::class);
        $validated = (new SpecificationValidator)->validate($specification = new Specification(), request()->all());

        $specification = DB::transaction(function () use(
            $validated, $specification
        ) {
            $specification->fill(
                $validated
            )->save();

            return $specification;
        });

        return SpecificationResource::make(
            $specification
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSpecificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSpecificationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Http\Response
     */
    public function show(Specification $specification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Http\Response
     */
    public function edit(Specification $specification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSpecificationRequest  $request
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSpecificationRequest $request, Specification $specification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Specification $specification)
    {
        //
    }
}
