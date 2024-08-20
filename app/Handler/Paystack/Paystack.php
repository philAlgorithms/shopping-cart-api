<?php

namespace App\Handler\Paystack;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\{ClientException, ConnectException};
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class Paystack
{
    /**
     * Issue Secret Key from your Paystack Dashboard
     * @var string
     */
    protected $secretKey;

    /**
     * Instance of Client
     * @var Client
     */
    protected $client;

    /**
     *  Response from requests made to Paystack
     * @var mixed
     */
    protected $response;

    /**
     * Paystack API base Url
     * @var string
     */
    protected $baseUrl;

    /**
     * Authorization Url - Paystack payment page
     * @var string
     */
    protected $authorizationUrl;

    /**
     * The preferred bank for creating DVA account 
     * @var string
     */
    protected $preferredBankSlug;

    public function __construct()
    {
        $this->setKey();
        $this->setBaseUrl();
        // $this->setRequestOptions();
        $this->setPreferredBankSlug();
    }

    /**
     * Get preffered bank slug from env
     */
    public function setPreferredBankSlug()
    {
        $this->preferredBankSlug = env('PAYSTACK_PREFERRED_BANK_SLUG', 'wema-bank');
    }

    /**
     * Get Base Url from Paystack env
     */
    public function setBaseUrl()
    {
        $this->baseUrl = env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co');
    }

    /**
     * Get secret key from Paystack config file
     */
    public function setKey()
    {
        $this->secretKey = env('PAYSTACK_SECRET_KEY', '');
    }

    /**
     * Set options for making the Client request
     */
    protected function setRequestOptions()
    {
        $authBearer = 'Bearer ' . $this->secretKey;

        $this->client = new Client(
            [
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => $authBearer,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json'
                ]
            ]
        );
    }

    /**
     * @param string $relativeUrl
     * @param string $method
     * @param array $body
     * @return Paystack
     * @throws IsNullException
     */
    protected function setHttpResponse($relativeUrl, $method, $body = [])
    {
        if (is_null($method)) {
            throw new Exception("Empty method not allowed");
        }

        try {
            $this->response = $this->client->{strtolower($method)}(
                $this->baseUrl . $relativeUrl,
                ["body" => json_encode($body)]
            );
        } catch (ClientException $e) {
            throw $e;
        }catch (ConnectException $e) {
            throw ValidationException::withMessages([
                'paystack' => 'Error in network connection.'
            ]);
        }

        return $this;
    }

    /**
     * Get the whole response from a get operation
     * @return array
     */
    protected function getResponse()
    {
        return json_decode($this->response->getBody(), true);
    }

    /**
     * Get details of a transaction carried out on your integration
     * 
     * @param int $id The transaction id/reference
     * @return array
     */
    public function fetchTransaction(int $id)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse("/transaction/{$id}", 'GET', [])->getResponse();
    }

    /**
     * Confirm the status of a transaction
     * 
     * @param string $reference The transaction id/reference
     * @return array
     */
    public function verifyTransaction(string $reference)
    {
        $this->setRequestOptions();
        return $this->setHttpResponse("/transaction/verify/{$reference}", 'GET', [])->getResponse();
    }
}
