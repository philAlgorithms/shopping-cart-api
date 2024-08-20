<?php

namespace App\Http\Controllers;

use App\Models\BuyerReferralProgram;
use App\Http\Requests\StoreBuyerReferralProgramRequest;
use App\Http\Requests\UpdateBuyerReferralProgramRequest;
use App\Http\Resources\BuyerReferralProgramResource;
use App\Models\Users\Buyer;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BuyerReferralProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $is_active = request('is_active');
        $programs = BuyerReferralProgram::query()
                ->when(
                    auth()->user() instanceof Buyer,
                    fn($builder) => $builder->where('buyer_id', auth()->id())
                )
                ->when(
                    request()->exists('is_active') && (is_bool($is_active) || $is_active === '1' || $is_active === '0'),
                    function($builder) use($is_active) {
                        if((bool)$is_active)
                        {
                            return $builder->whereNotNull('activated_at');
                        }else
                        {
                            // Improve this query later to match one in the model
                            return $builder->whereNull('activated_at'); 
                        }
                    }
                )
                ->paginate(getpaginator(request()));

        return BuyerReferralProgramResource::collection(
            $programs
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BuyerReferralProgram  $buyerReferralProgram
     * @return \Illuminate\Http\Response
     */
    public function show(BuyerReferralProgram | string $buyerReferralProgram)
    {
        $ref = gettype((int)$buyerReferralProgram) == 'integer' && (int)$buyerReferralProgram ? BuyerReferralProgram::find($buyerReferralProgram) : BuyerReferralProgram::firstWhere('code', $buyerReferralProgram);
        // return gettype(is_int($buyerReferralProgram));
        if(is_null($ref)) return response(status: 404);

        $this->authorize('view', $ref);

        return BuyerReferralProgramResource::make(
            $ref
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', [BuyerReferralProgram::class]);

        $user = auth()->user()->user;
        $program = new BuyerReferralProgram([
            'buyer_id' => auth()->user()->id,
            'code' => strtolower(implode("-", [$user->first_name, $user->last_name, Str::random(8)])),
            'activated_at' => now()
        ]);

        $program = DB::transaction(function () use (
            $program
        ) {
            $program->save();

            return $program;
        });

        return BuyerReferralProgramResource::make(
            $program
        );
    }

    /**
     * Activate a referral program request.
     *
     * @param  \App\Models\BuyerReferralProgram  $buyerReferralProgram
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function activate(BuyerReferralProgram $buyerReferralProgram)
    {
        $this->authorize(
            'activate',
            $buyerReferralProgram
        );

        $buyerReferralProgram->activate(auth()->user());

        return BuyerReferralProgramResource::make(
            $buyerReferralProgram
        );
    }

    /**
     * Deactivate a referral program request.
     *
     * @param  \App\Models\BuyerReferralProgram  $buyerReferralProgram
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deactivate(BuyerReferralProgram $buyerReferralProgram)
    {
        $this->authorize(
            'activate',
            $buyerReferralProgram
        );

        $buyerReferralProgram->deactivate(auth()->user());

        return BuyerReferralProgramResource::make(
            $buyerReferralProgram
        );
    }
}
