<?php

use App\Models\Currency;

/**
 * Gets the app's default currency
 * 
 * @return App\Models\Currency|null
 */
function get_default_currency(): Currency|null
{
    return Currency::firstWhere('acronym', env('DEFAULT_CURRENCY'));
}