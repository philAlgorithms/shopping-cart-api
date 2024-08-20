<?php

namespace App\Http\Controllers;

use App\Models\Payments\WalletFunding;
use App\Http\Requests\StoreWalletFundingRequest;
use App\Http\Requests\UpdateWalletFundingRequest;
use App\Http\Resources\Payments\WalletFundingResource;
use App\Models\Currency;
use App\Models\Order\Order;
use App\Models\Payments\PaystackPayment;
use App\Models\Users\Buyer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Unicodeveloper\Paystack\{Paystack, TransRef};

class WalletFundingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $is_buyer = $user instanceof Buyer;

        $fundings = WalletFunding::query()
            ->when(
                $is_buyer,
                fn ($builder) => $builder->where('buyer_id', $user->id)
            )
            ->paginate(getpaginator(request()));

        return WalletFundingResource::collection(
            $fundings
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreWalletFundingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWalletFundingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payments\WalletFunding  $walletFunding
     * @return \Illuminate\Http\Response
     */
    public function show(WalletFunding $walletFunding)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payments\WalletFunding  $walletFunding
     * @return \Illuminate\Http\Response
     */
    public function edit(WalletFunding $walletFunding)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWalletFundingRequest  $request
     * @param  \App\Models\Payments\WalletFunding  $walletFunding
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWalletFundingRequest $request, WalletFunding $walletFunding)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payments\WalletFunding  $walletFunding
     * @return \Illuminate\Http\Response
     */
    public function destroy(WalletFunding $walletFunding)
    {
        //
    }

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function fund()
    {
        $validated = validator(
            request()->all(),
            [
                'amount' => ['required', 'numeric', 'min:1000'],
                'callback_url' => ['sometimes', 'string', 'active_url'],
            ],
            [
                'amount' => 'The minimum amount you can fund is 1,000 NGN'
            ]
        )->validate();

        $user = auth()->user();
        return new JsonResource(["url" => $user->fundWallet($validated['amount'])->getTargetUrl()]);
    }

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function fundForInstallment(Order $order)
    {
        $validated = validator(
            request()->all(),
            [
                'amount' => ['required', 'numeric', 'min:1000']
            ],
            [
                'amount' => 'The minimum amount you can fund is 1,000 NGN'
            ]
        )->validate();

        $user = auth()->user();
        $ref = TransRef::getHashedToken();
        $paystackPayment = PaystackPayment::create([
            "reference" => $ref
        ]);

        WalletFunding::create([
            'paystack_payment_id' => $paystackPayment->id,
            'buyer_id' => $user->id,
            'amount' => $validated['amount'],
            'currency_id' => Currency::firstWhere('acronym', 'NGN')->id
        ]);
        try {
            $paystack = (new Paystack);
            $data = [
                "amount" => $validated['amount'] * 100,
                "reference" => $ref,
                "email" => $user->email,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "currency" => "NGN",
            ];
            return $paystack->getAuthorizationUrl($data)->redirectNow();
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['paystack' => 'The paystack token has expired. Please refresh the page and try again.']);
        }
    }
}
