<?php
namespace App\Handler\Paystack;

use App\Models\{Currency, User};
use App\Models\Location\Country;

class PaystackMiscellaneous extends Paystack
{
    /**
     * Get a list of all supported banks and their properties
     * 
     * @return array
     */
    public function listBanks(
        Country|null $country=null,
        Currency|null $currency=null,
        bool|null $pay_with_bank_transfer=null,
        bool|null $pay_with_bank=null,
        bool|null $use_cursor=null,
        int $per_page=50
    )
    {
        $query_string = "?use_cursor={$use_cursor}&perPage={$per_page}";
        $query_string .= !is_null($country) ? '&country=' . $country->nicename : '';
        $this->setRequestOptions();
        return $this->setHttpResponse(
            "/bank{$query_string}",
            'GET',
            [
                'pay_with_bank' => $pay_with_bank,
                'pay_with_bank_transfer' => $pay_with_bank_transfer,
                'currency' => $currency->acronym ?? null
            ]
        )->getResponse();
    }

    /**
     * Gets a list of countries that Paystack currently supports
     * @return array
     */
    public function listCountries()
    {
        $this->setRequestOptions();
        return $this->setHttpResponse('/country', 'GET', [])->getResponse();
    }

    /**
     * Get a list of states for a country for address verification.
     * @return array
     */
    public function listStates(Country $country)
    {

        $this->setRequestOptions();
        return $this->setHttpResponse("/address_verification/states?country=CA", 'GET', [])->getResponse();
    }
}