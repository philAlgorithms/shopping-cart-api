<?php

namespace App\Handler\Paystack;

use App\Models\{Currency, User};

class PaystackDVA extends Paystack
{
    /**
     * Create a dedicated virtual account for an existing customer
     * 
     * @param string $customer Customer id or code
     * @param string|null $preferred_bank The bank slug for preferred bank.
     * @param string|null $first_name Customer's first name
     * @param string|null $last_name Customer's last name
     * @param string|null $phone_number Customer's phone_number
     * 
     * @return array
     */
    public function create(
        string $customer,
        string $preferred_bank='wema-bank',
        string|null $first_name=null,
        string|null $last_name=null,
        string|null $phone_number=null
    ) {
        $data = [
            "customer" => $customer,
            "preferred_bank" => $preferred_bank,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "phone" => $phone_number
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/dedicated_account', 'POST', $data)->getResponse();
    }

    /**
     * Create a customer, validate the customer, and assign a DVA to the customer.
     * 
     * @param string $customer Customer id or code
     * @param string $preferred_bank The bank slug for preferred bank.
     * @param string $first_name Customer's first name
     * @param string $last_name Customer's last name
     * @param string $phone_number Customer's phone_number
     * @param string|null $account_number Customer's account number
     * @param string|null $bank_code Customer's bank code
     * @param string|null $bvn Customer's bvn
     * 
     * @return array
     */
    public function assign(
        string $customer,
        string|null $first_name,
        string|null $last_name,
        string|null $phone_number,
        string $preferred_bank='wema-bank',
        string|null $account_number=null,
        string|null $bank_code=null,
        string|null $bvn=null
    ) {
        $data = [
            "customer" => $customer,
            "preferred_bank" => $preferred_bank,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "phone" => $phone_number,
            "account_number" => $account_number,
            "bank_code" => $bank_code,
            "bvn" => $bvn
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/dedicated_account/assign', 'POST', $data)->getResponse();
    }

    /**
     * List dedicated virtual accounts available on your integration
     * 
     * @param bool|null $status Status of the dedicated virtual account
     * @param string|null $currency The currency of the dedicated virtual account. Only NGN is currently allowed
     * 
     * @return array
     */
    public function fetchAll(bool|null $active = null, string|null $currency = null)
    {
        $query_string = '?';

        $query_string .= !is_null($active) ? "use_cursor={$active}" : '';
        $query_string .= !is_null($currency) ? "?currency={$currency}" : '';

        $this->setRequestOptions();
        return $this->setHttpResponse("/dedicated_account{$query_string}", 'GET', [])->getResponse();
    }

    /**
     * Get details of a dedicated virtual account on your integration.
     * 
     * @param $id ID of dedicated virtual account
     * @return array
     */
    public function fetch($id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse("/dedicated_account/{$id}", 'GET', [])->getResponse();
    }

    /**
     * Requery Dedicated Virtual Account for new transactions
     * 
     * @param string $account_number Virtual account number to requery
     * @param string $provider_slug The bank's slug in lowercase, without spaces e.g. wema-bank
     * @param string|null $date The day the transfer was made in YYYY-MM-DD format
     * 
     * @return array
     */
    public function requery(string $account_number, string $provider_slug, string|null $date = null)
    {
        $query_string = "?account_number={$account_number}&provider_slug={$provider_slug}";

        $query_string .= !is_null($date) ? "&date={$date}" : '';

        $this->setRequestOptions();
        return $this->setHttpResponse("/dedicated_account/requery{$query_string}", 'GET', [])->getResponse();
    }

    /**
     * Deactivate a dedicated virtual account on your integration.
     * 
     * @param int $id ID of dedicated virtual account
     * 
     * @return array
     */
    public function deactivate(int $id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse("/dedicated_account/{$id}", "DELETE", [])->getResponse();
    }

    /**
     * Get available bank providers for a dedicated virtual account
     * 
     * @return array
     */
    public function fetchBankProviders()
    {
        $this->setRequestOptions();
        return $this->setHttpResponse("/dedicated_account/available_providers", "GET", [])->getResponse();
    }
}
