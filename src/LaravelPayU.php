<?php


namespace TooPago\Payu;

use Exception;

class LaravelPayU
{
    /**
     * The payu merchant ID.
     *
     * @var string
     */
    protected static $merchantId;

    /**
     * The payu API login.
     *
     * @var string
     */
    protected static $apiLogin;

    /**
     * The payu API Key.
     *
     * @var string
     */
    protected static $apiKey;

    /**
     * The payu Account ID.
     *
     * @var string
     */
    protected static $accountId;


    protected static $BUYER_EMAIL;
    protected static $PAYER_NAME;
    protected static $PAYER_DNI;

    /**
     * The country where the payment is processed.
     *
     * @var string
     */
    protected static $country;

    /**
     * The current currency.
     *
     * @var string
     */
    protected static $currency;

    /**
     * The current currency symbol.
     *
     * @var string
     */
    protected static $currencySymbol = '$';

    /**
     * The custom currency formatter.
     *
     * @var callable
     */
    protected static $formatCurrencyUsing;

    /**
     * Default validation rules (for testing purposes).
     *
     * @var array
     */
    protected static $rules;

    /**
     * The account testing state.
     *
     * @var bool
     */
    protected static $isTesting;

    /**
     * Set the currency to be used when billing users.
     *
     * @param  string  $currency
     * @param  string|null  $symbol
     * @return void
     */
    public static function useCurrency($currency, $symbol = null)
    {
        static::$currency = $currency;

        static::useCurrencySymbol($symbol ?: static::guessCurrencySymbol($currency));
    }

    /**
     * Guess the currency symbol for the given currency.
     *
     * @param  string  $currency
     * @return string
     */
    protected static function guessCurrencySymbol($currency)
    {
        switch (strtolower($currency)) {
        case 'usd':
        case 'clp':
        case 'cop':
        case 'mxn':
            return '$';
        case 'ars':
            return '$a';
        case 'pen':
            return 'S/';
        case 'brl':
            return 'R$';
        default:
            throw new Exception('Unable to guess symbol for currency. Please explicitly specify it.');
        }
    }

    /**
     * Get the currency currently in use.
     *
     * @return string
     */
    public static function usesCurrency()
    {
        return static::$currency;
    }

    /**
     * Set the currency symbol to be used when formatting currency.
     *
     * @param  string  $symbol
     * @return void
     */
    public static function useCurrencySymbol($symbol)
    {
        static::$currencySymbol = $symbol;
    }

    /**
     * Set the custom currency formatter.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function formatCurrencyUsing(callable $callback)
    {
        static::$formatCurrencyUsing = $callback;
    }

    /**
     * Format the given amount into a displayable currency.
     *
     * @param  int  $amount
     * @return string
     */
    public static function formatAmount($amount)
    {
        if (static::$formatCurrencyUsing) {
            return call_user_func(static::$formatCurrencyUsing, $amount);
        }

        $amount = number_format($amount / 100, 2);

        if (starts_with($amount, '-')) {
            return '-'.static::usesCurrencySymbol().ltrim($amount, '-');
        }

        return static::usesCurrencySymbol().$amount;
    }



    public static function getBUYER_EMAIL()
    {
        if (static::$BUYER_EMAIL) {
            return static::$BUYER_EMAIL;
        }
        if ($BUYER_EMAIL = getenv('PAYU_BUYER_EMAIL')) {
            return $BUYER_EMAIL;
        }
        return getenv('PAYU_BUYER_EMAIL');
        //return config('payu.payu_buyer_email');
    }

    public static function getPAYER_NAME()
    {
        if (static::$PAYER_NAME) {
            return static::$PAYER_NAME;
        }
        if ($PAYER_NAME = getenv('PAYU_PAYER_NAME')) {
            return $PAYER_NAME;
        }
        return getenv('PAYU_PAYER_NAME');
        //return config('payu.payu_payer_name');
    }

    public static function getPAYER_DNI()
    {
        if (static::$PAYER_DNI) {
            return static::$PAYER_DNI;
        }
        if ($PAYER_DNI = getenv('PAYU_PAYER_DNI')) {
            return $PAYER_DNI;
        }
        return getenv('PAYU_PAYER_DNI');
        //return config('payu.payu_payer_dni');
    }

    





    /**
     * Get the Account ID.
     *
     * @return string
     */
    public static function getAccountId()
    {
        if (static::$accountId) {
            return static::$accountId;
        }
        if ($accountId = getenv('PAYU_ACCOUNT_ID')) {
            return $accountId;
        }
        return getenv('PAYU_ACCOUNT_ID');
        //return config('payu.payu_account_id');
    }

    /**
     * Get the Merchant ID.
     *
     * @return string
     */
    public static function getMerchantId()
    {
        if (static::$merchantId) {
            return static::$merchantId;
        }
        if ($merchantId = getenv('PAYU_MERCHANT_ID')) {
            return $merchantId;
        }

        return getenv('PAYU_MERCHANT_ID');
        return config('payu.payu_merchant_id');
    }

    /**
     * Get the API login.
     *
     * @return string
     */
    public static function getApiLogin()
    {
        if (static::$apiLogin) {
            return static::$apiLogin;
        }
        if ($apiLogin = getenv('PAYU_API_LOGIN')) {
            return $apiLogin;
        }
        return getenv('PAYU_API_LOGIN');
        //return config('payu.payu_api_login');
    }

    /**
     * Get the API key.
     *
     * @return string
     */
    public static function getApiKey()
    {
        if (static::$apiKey) {
            return static::$apiKey;
        }
        if ($apiKey = getenv('PAYU_API_KEY')) {
            return $apiKey;
        }
        return getenv('PAYU_API_KEY');
        //return config('payu.payu_api_key');
    }

    /**
     * Get the Account country.
     *
     * @return string
     */
    public static function getCountry()
    {
        if (static::$country) {
            return static::$country;
        }
        if ($country = getenv('PAYU_COUNTRY')) {
            return $country;
        }
        return getenv('PAYU_COUNTRY');
        //return config('payu.payu_country');
    }

    /**
     * Get the PSE redirect URL.
     *
     * @return string
     */
    public static function getRedirectPSE()
    {
        if ($pseRedirect = getenv('PSE_REDIRECT_URL')) {
            return $pseRedirect;
        }
        return getenv('PSE_REDIRECT_URL');
        //return config('payu.pse_redirect_url');
    }

    /**
     * Set the Account testing state (never use on production)
     *
     * @return string
     */
    public static function setAccountOnTesting($state)
    {
        static::$isTesting = $state;
    }

    /**
     * Get the account testing value.
     *
     * @return string
     */
    private static function isAccountInTesting()
    {
        if (!is_null(static::$isTesting)) {
            return static::$isTesting;
        }
        if ($isTesting = getenv('PAYU_ON_TESTING')) {
            return $isTesting;
        }
        //return 'false';
        return getenv('PAYU_ON_TESTING');
        //return config('payu.payu_testing');
    }

    /**
     * Get the app testing value.
     *
     * @return string
     */
    private static function isAppInTesting()
    {
        if ($isLocal = getenv('APP_ENV')) {
            return $isLocal;
        }
        return getenv('APP_ENV');
        //return config('app.env');
    }

    /**
     * Check if PayU platform available.
     *
     * @return PayU RequestPaymentsUtil
     */
    public static function doPing($onSuccess, $onError)
    {
        static::setPayUEnvironment();
        try {
            $response = \PayUPayments::doPing();
            if ($response) {
                $onSuccess($response);
            }
        } catch (\PayUException $exc) {
            $onError($exc);
        }
    }




    /**
     * Set PayU Environment for the account.
     *
     * @return void
     */
    public static function setPayUEnvironment()
    {
        \PayU::$apiKey = static::getApiKey();
        \PayU::$apiLogin = static::getApiLogin();
        \PayU::$merchantId = static::getMerchantId();
        \PayU::$isTest = static::isAccountInTesting();
        \PayU::$language = \SupportedLanguages::ES;
        if (static::isAppInTesting() == 'local') {
            \Environment::setPaymentsCustomUrl(
                "https://sandbox.api.payulatam.com/payments-api/4.0/service.cgi"
            );
            \Environment::setReportsCustomUrl(
                "https://sandbox.api.payulatam.com/reports-api/4.0/service.cgi"
            );
            \Environment::setSubscriptionsCustomUrl(
                "https://sandbox.api.payulatam.com/payments-api/rest/v4.9/"
            );
        } else {
            \Environment::setPaymentsCustomUrl(
                "https://api.payulatam.com/payments-api/4.0/service.cgi"
            );
            \Environment::setReportsCustomUrl(
                "https://api.payulatam.com/reports-api/4.0/service.cgi"
            );
            \Environment::setSubscriptionsCustomUrl(
                "https://api.payulatam.com/payments-api/rest/v4.9/"
            );
        }
    }
}
