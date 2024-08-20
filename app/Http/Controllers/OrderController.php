<?php

namespace App\Http\Controllers;

use App\Models\Order\{Coupon, Order};
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Cart;
use App\Models\Currency;
use App\Models\Payments\{PaystackPayment, PaystackPurchase};
use App\Models\Users\Buyer;
use App\Models\Users\Vendor;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Unicodeveloper\Paystack\Paystack;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $user = auth()->user();
        $is_buyer = $user instanceof Buyer;
        $is_vendor = $user instanceof Vendor;

        $orders = Order::query()
            ->when(
                $is_buyer,
                fn ($builder) => $builder->where('buyer_id', $user->id)
            )
            ->when(
                $is_vendor,
                fn ($builder) => $builder->whereHas(
                    'cart',
                    fn ($query) => $query->whereHas(
                        'product',
                        fn ($q) => $q->whereHas(
                            'store',
                            fn ($s) => $s->where('vendor_id', $user->id)
                        )
                    )
                )
            )
            ->paginate(getpaginator(request()));

        return OrderResource::collection(
            $orders
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
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return OrderResource::make(
            $order
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        $order->waybill()->delete();
        $order->homeDelivery()->delete();


        $cart = $order->cart;
        $deleted = $order->delete();
        foreach ($cart as $item) {
            try {
                $item->product->addTocart($item->quantity);
            } catch (ValidationException $e) {
                // Validation exception thrown in the addToCart() method guard against
                // adding items that are out of stock. Hence this block of code prevents
                // the loop from terminating when such an error is caught.
            }
        }

        return $deleted;
    }

    /**
     * Creates and order an fills it with cart items
     * 
     * @return \App\Models\Order\Order
     */
    public function checkout()
    {
        $preValidated = validator(
            request()->all(),
            [
                'coupon_code' => ['sometimes', 'string', Rule::exists('coupons', 'code')],
            ]
        )->validate();

        $coupon = null;
        if (array_key_exists('coupon_code', $preValidated)) {
            $coupon = Coupon::firstWhere('code', $preValidated['coupon_code']);
        }

        $this->authorize('checkout', [Order::class, $coupon]);

        // Since it is not mandated that buyers should provide their address, the shipping address shall
        // always be required on checkout. This address can always be updated using the updateShipping() method.
        $validated = validator(
            request()->all(),
            [
                'installments' => ['sometimes', 'integer', 'min:1', 'max:4'],
                'shipping_address' => ['required', 'string', 'max:255'],
                'delivery_town_id' => ['required', 'numeric', Rule::exists('towns', 'id')],
                'home_delivery' => ['sometimes', 'boolean'],
            ]
        )->validate();

        $buyer = auth()->user();

        $installments = array_key_exists('installments', $validated) ? $validated['installments'] : 1;

        if (! is_null($coupon)) {
            $validated['coupon_id'] = $coupon->id;
        }
        $order = new Order([
            'buyer_id' => $buyer->id,
            'installments' => $installments,
            ...Arr::only($validated, ['shipping_address', 'delivery_town_id', 'home_delivery', 'coupon_id'])
        ]);
        $order->save();
        $order->fillCart();

        // Add the cost of shipping by creating an `order_journey`
        $order->initiateWaybill();
        $order->initiateHomeDelivery();

        return OrderResource::make(
            $order->refresh()
        );
    }
    /**
     * Updates shipping information
     * 
     * @return \App\Models\Order\Order
     */
    public function updateShipping(Order $order)
    {
        $this->authorize('updateShipping', $order);
        $validated = validator(
            request()->all(),
            [
                'shipping_address' => ['required', 'string', 'max:255'],
                'delivery_town_id' => ['required', 'numeric', Rule::exists('towns', 'id')],
                'home_delivery' =>  ['sometimes', 'boolean']
            ]
        )->validate();

        $order->update([
            'shipping_address' => $validated['shipping_address'],
            'delivery_town_id' => $validated['delivery_town_id'],
            'home_delivery' => array_key_exists('home_delivery', $validated) ? $validated['home_delivery'] : 0
        ]);
        $order->save();
        $order->refresh();

        if (array_key_exists('home_delivery', $validated)) {
            // Create home delivery with fixed amount
        }

        return OrderResource::make(
            $order->refresh()
        );
    }


    /**
     * Pays for an order
     * 
     */
    public function pay(Order $order)
    {
        $this->authorize('pay', $order);

        // return $order->pay();
        return new JsonResource(["url" => $order->pay()->getTargetUrl()]);
    }

    /**
     * Pays for an order
     * 
     */
    public function payFromWallet(Order $order)
    {
        $this->authorize('payFromWallet', $order);

        return $order->payFromWallet();
    }

    /**
     * Legacy code that tries to check if an order has already been paid for.
     * This method is deprecated since installment payments are now funded in the wallet.
     */
    public function payAlt(Order $order)
    {
        $this->authorize('pay', $order);
        $user = auth()->user();
        $payable_amount = $order->total / $order->installments;
        if (is_null(PaystackPayment::firstWhere(["reference" => $order->reference]))) {
            $paystackPayment = PaystackPayment::create([
                "reference" => $order->reference
            ]);

            $order->paystackPurchases()->create([
                'paystack_payment_id' => $paystackPayment->id,
                'amount' => $payable_amount,
                'currency_id' => Currency::firstWhere('acronym', 'NGN')->id
            ]);
        }

        try {
            $paystack = (new Paystack);
            $data = [
                "amount" => $payable_amount * 100,
                "reference" => $order->reference,
                "email" => $user->email,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                // "callback_url" => request()->callback_url,
                "currency" => "NGN",
            ];
            $tx = $order->verifyTransaction();

            if ($tx['status'] && $tx['data']['status'] == 'abandoned') {
                $newRef = $paystack->genTranxRef();
                $order->update(['reference' => $newRef]);
                $paystackPayment->update(['reference' => $newRef]);

                try {
                    $data['reference'] = $newRef;
                    return $paystack->getAuthorizationUrl($data)->redirectNow();
                } catch (ClientException $e) {
                    $response = (array)json_decode($e->getResponse()->getBody()->getContents());
                    if ($e->getResponse()->getStatusCode() == 400) {
                        if (array_key_exists('message', $response) && $response['message'] == 'Duplicate Transaction Reference') {
                            return $order->pay();
                        }
                    }
                    throw $e;
                } catch (\Exception $e) {
                    throw ValidationException::withMessages(['paystack' => 'Some error occured. Please try again.']);
                }
            }
            return $order->pay();
        } catch (ClientException $e) {
            $response = (array)json_decode($e->getResponse()->getBody()->getContents());

            if (array_key_exists('message', $response)) {
                if ($response['message'] == 'Duplicate Transaction Reference') {
                    return $order->pay();
                } else if ($response['message'] == 'Transaction reference not found') {
                    // return $response;
                    return $paystack->getAuthorizationUrl($data)->redirectNow();
                }
            }
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['paystack' => 'Some error occured. Please try again.']);
        }
    }

    public function paystackPay(PaystackPurchase $paystackPurchase)
    {
        $this->authorize('pay', $paystackPurchase);

        return $paystackPurchase->pay();
    }

    public function verifyTransaction(Order $order)
    {
        $this->authorize('verifyTransaction', $order);

        if (is_null($order)) return response([]);
        try {
            return $order->verifyTransaction();
        } catch (ClientException $e) {
            return response((array)json_decode($e->getResponse()->getBody()->getContents()), 400);
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['paystack' => 'Some error occured. Please try again.']);
        }
    }
}
