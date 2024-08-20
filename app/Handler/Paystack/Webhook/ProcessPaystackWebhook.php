<?php

namespace App\Handler\Paystack\Webhook;

use App\Models\Payments\{PaystackPayment, PaystackPurchase, WalletFunding};
use App\Models\{User};
use Illuminate\Support\Facades\{DB, Log};
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

//The class extends "ProcessWebhookJob" class as that is the class //that will handle the job of processing our webhook before we have //access to it.
class ProcessPaystackWebhook extends ProcessWebhookJob
{
    public function handle()
    {
        $data = json_decode($this->webhookCall, true);

        // Do something with the payload
        $event = $data['payload']['event'];
        $payload = $data['payload']['data'];
        $event_type = stringBefore($event, ".");

        // If CUSTOMER BVN VALIDATION
        if ($event_type === 'customeridentification') {
            $user = User::firstWhere('paystack_customer_code', $payload['customer_code']);

            switch ($event) {
                case "customeridentification.success": {
                        if (!is_null($user)) {
                            $user->update([
                                'bvn' => $user->last_uploaded_bvn,
                                'bank_account_number' => $user->last_uploaded_bank_account_number,
                                'bank_id' => $user->last_uploaded_bank_id,
                                'bvn_verified_at' => now()
                            ]);

                            // Send bvn update mail
                        }
                    }
                    break;
                case "customeridentification.failed":
                    // Do something when customer validation failed
                    break;
            }
        } else if ($event_type === 'charge' && array_key_exists('reference', $payload)) {
            $reference = $payload['reference'];
            $payable_type = $payload['metadata']['payable_type'];
            $payable_load = $payload['metadata']['payable_load'];
            $possible_paystack_payment = PaystackPayment::firstWhere('reference', $reference);

            // Save payment if reference does not already exist in the system.
            if (is_null($possible_paystack_payment)) {
                switch ($event) {
                    case "charge.success": {
                            $paystack_payment = PaystackPayment::create([
                                'reference' => $reference,
                                'payload' => $payload,
                            ]);
                            switch ($payable_type) {
                                case WalletFunding::class: {
                                        // Handle wallet funding
                                        DB::transaction(function () use ($paystack_payment, $payload, $payable_load) {
                                            $funding = WalletFunding::create([
                                                ...$payable_load,
                                                ...[
                                                    'amount' => $payload['amount'] / 100,
                                                    'paystack_payment_id' => $paystack_payment->id,
                                                ]
                                            ]);
                                        });
                                        break;
                                    }
                                case PaystackPurchase::class: {
                                        // One-time order payment
                                        DB::transaction(function () use ($paystack_payment, $payload, $payable_load) {
                                            $purchase = PaystackPurchase::create([
                                                ...$payable_load,
                                                ...[
                                                    'amount' => $payload['amount'] / 100,
                                                    'paystack_payment_id' => $paystack_payment->id,
                                                ]
                                            ]);
                                        });
                                        break;
                                    }
                                default: {
                                        // Notify the admin of an unknown payment
                                    }
                            }

                            break;
                        }
                    case "charge.failed": {
                            // Handle payment failure
                        }
                }
            }
        }

        http_response_code(200); //Acknowledge you received the response
    }
}
