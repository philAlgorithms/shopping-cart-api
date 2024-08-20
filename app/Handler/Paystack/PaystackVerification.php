<?php

namespace App\Handler\Paystack;

use App\Models\{Currency, User};

class PaystackVerification extends Paystack
{
    /**
     * Confirm an account belongs to the right customer
     * @return array
     */
    public function resolveAccount(string $account_number, string $bank_code)
    {
        $query_string = "?account_number={$account_number}&bank_code={$bank_code}";

        $this->setRequestOptions();
        return $this->setHttpResponse("/bank/resolve{$query_string}", 'GET', [])->getResponse();
    }

    /**
     * Confirm an account belongs to the right customer
     * @param string $account_name Customer's first and last name registered with their bank
     * @param string $account_number Customer's bank account number
     * @param string $bank_code
     * @param string $account_type This can take one of: [ personal, business ]
     * @param string $country_code The two digit ISO code of the customer’s country
     * @param string $document_type Customer’s mode of identity. This could be one of: [ identityNumber, passportNumber, businessRegistrationNumber ]
     * @param string|null $document_number Customer’s mode of identity number
     * @return array
     */
    public function validateAccount(string $account_name, string $account_number, string $bank_code, string $account_type, string $country_code, string $document_type, string|null $document_number = null)
    {
        $data = [
            'account_name' => $account_name,
            'account_number' => $account_number,
            'bank_code' => $bank_code,
            'account_type' => $account_type,
            'country_code' => $country_code,
            'document_type' => $document_type,
            'document_number' => $document_number
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse("/bank/validate", 'POST', $data)->getResponse();
    }

    /**
     * Get more information about a customer's card
     * 
     * @param string $bin First 6 characters of card
     * @return array
     */
    public function resolveCardBin(string $bin)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse("/decision/bin/{$bin}", 'GET', [])->getResponse();
    }
}
