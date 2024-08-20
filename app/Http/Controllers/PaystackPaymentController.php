<?php

namespace App\Http\Controllers;

use App\Models\Payments\PaystackPayment;
use App\Http\Requests\StorePaystackPaymentRequest;
use App\Http\Requests\UpdatePaystackPaymentRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Unicodeveloper\Paystack\Paystack;

class PaystackPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StorePaystackPaymentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaystackPaymentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payments\PaystackPayment  $paystackPayment
     * @return \Illuminate\Http\Response
     */
    public function show(PaystackPayment $paystackPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payments\PaystackPayment  $paystackPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(PaystackPayment $paystackPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePaystackPaymentRequest  $request
     * @param  \App\Models\Payments\PaystackPayment  $paystackPayment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaystackPaymentRequest $request, PaystackPayment $paystackPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payments\PaystackPayment  $paystackPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaystackPayment $paystackPayment)
    {
        //
    }

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        $paystack = (new Paystack);
        $data = [
            "amount" => 20,
            "reference" => $paystack->genTranxRef(),
            "email" => "philippos764@gmail.com",
            "first_name" => "Nwokedi",
            "last_name" => "Philip",
            // "callback_url" => request()->callback_url,
            "currency" => "NGN",
        ];

        try{
            $paystack->getAuthorizationUrl($data);
            return $paystack->url;
        }catch(\Exception $e) {
            return $e->getMessage();
            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }        
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = (new Paystack)->getPaymentData();

        if(!is_null($paymentDetails['data']) && !is_null($paymentDetails['data']['reference']))
        {
            $payment = PaystackPayment::firstWhere('reference', $paymentDetails['data']['reference']);

            if(! is_null($payment))
            {
                $payment->handleCallback($paymentDetails['data'], false);

                return $payment;
            }

            throw ValidationException::withMessages([
                'paystack' => 'Unable to retrieve payment data'
            ]);
        }
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
    }

    /**
     * Save paystack payment after signature verification
     */
    private function savePayment(array $input)
    {
        dd($input);
    }

    /**
     * Handle paystack webhook
     */
    public function webhook()
    {
        $input = @file_get_contents("php://input");
        // $signature = request()->header('x-paystack-signature');

        if(($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, env('PAYSTACK_SECRET_KEY'))))
        {
            exit(400);
        }

        return $this->savePayment(request()->all());
    }
}
