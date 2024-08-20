<?php

namespace App\Exceptions\Stores;

use Exception;

class VendorHasNoStoreException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }
 
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return false; //response([], 406);
    }
 
    /**
     * Get the exception's context information.
     *
     * @return array
     */
    public function context()
    {
        return ['vendor_id' => '$this->orderId'];
    }
}
