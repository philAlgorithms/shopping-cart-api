<?php

namespace App\Handler\Paystack;

class PaystackCustomer extends Paystack
{
    /**
     * Create a customer
     * 
     * @param string $email Customer email
     * @param string $first_name Customer's first name
     * @param string $last_name Customer's last name
     * @param string $phone_number Customer's phone number
     * @param array $metadata Key => value pairs array of additional information
     * 
     * @return array
     */
    public function create(
        string $email,
        string $first_name,
        string $last_name,
        string $phone_number,
        array $metadata = []
    ) {
        $data = [
            "email" => $email,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "phone" => $phone_number,
            // "metadata" => json_encode($metadata)
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/customer', 'POST', $data)->getResponse();
    }

    /**
     * Fetch a customer based on id or code
     * @param $id Email or unique id of the customer
     * @return array
     */
    public function fetch(string $id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/customer/' . $id, 'GET', [])->getResponse();
    }

    /**
     * Fetch a customer based on id or code
     * @param $id Email or unique id of the customer
     * @return array
     */
    public function fetchAll()
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/customer', 'GET', [])->getResponse();
    }

    /**
     * Update a customer's details
     * 
     * @param string|null $email Customer email
     * @param string|null $first_name Customer's first name
     * @param string|null $last_name Customer's last name
     * @param string|null $phone_number Customer's phone number
     * @param array $metadata Key => value pairs array of additional information
     * 
     * @return array
     */
    public function update(
        string|null $email=null,
        string|null $first_name=null,
        string|null $last_name=null,
        string|null $phone_number=null,
        array $metadata = []
    ) {
        $data = [
            "email" => $email,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "phone" => $phone_number,
            "metadata" => $metadata
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse('/customer', 'PUT', $data)->getResponse();
    }

    /**
     * Confirm an customer account with bvn
     * 
     * @param string $customer_code Customer's first and last name registered with their bank
     * @param string $first_name
     * @param string $last_name
     * @param string $bvn
     * @param string $account_number Customer's bank account number
     * @param string $bank_code
     * @param string $account_type This can take one of: [ personal, business ]
     * @param string $country The two digit ISO code of the customer’s country
     * @param string $type Predefined types of identification. Only 'bank_account' is supported at the moment
     * @return array
     */
    public function validate(
        string $customer_code,
        string $first_name,
        string $last_name,
        string $bvn,
        string $account_number,
        string $bank_code,
        string | null $country = 'NG',
        string | null $type = "bank_account"
    ) {
        $data = [
            'customer_code' => $customer_code,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'account_number' => $account_number,
            'bank_code' => $bank_code,
            'type' => $type,
            'country' => $country,
            'bvn' => $bvn,
        ];

        $this->setRequestOptions();
        return $this->setHttpResponse("/customer/{$customer_code}/identification", 'POST', $data)->getResponse();
    }
}
