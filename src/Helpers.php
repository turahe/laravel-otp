<?php

if (! function_exists('validation_number')) {
    /**
     * Validate phone number
     *
     * @param string|int $number
     * @return bool
     * @throws \libphonenumber\NumberParseException
     */
    function validation_number(string|int $number): bool
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $parseNumber = $phoneUtil->parse($number, mb_strtoupper(config('app.locale', 'id')));
        $phoneNumber = $phoneUtil->isValidNumber($parseNumber); // true

        return (strlen($parseNumber->getNationalNumber()) < 11 || strlen($parseNumber->getNationalNumber()) >= 12) && $phoneNumber;
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format phone number
     *
     * @param string|int $number
     * @return string
     * @throws \libphonenumber\NumberParseException
     */
    function format_phone(string|int $number): string
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $parseNumber = $phoneUtil->parse($number, 'ID');
        return $phoneUtil->format($parseNumber, \libphonenumber\PhoneNumberFormat::E164); // +62812341234
    }
}

if (!function_exists('format_whatsapp')) {
    /**
     * Format phone number
     *
     * @param $number
     * @return string
     * @throws \libphonenumber\NumberParseException
     */
    function format_whatsapp($number): string
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $parseNumber = $phoneUtil->parse($number, 'ID');
        $phoneNumber = $phoneUtil->format($parseNumber, \libphonenumber\PhoneNumberFormat::E164); // +62812341234

        return str_replace('+', '', $phoneNumber);
    }
}

if (! function_exists('get_email_provider')) {
    /**
     * Get single or multiple values from settings table
     */
    function get_email_provider(string $email): string
    {
        $provider = explode('@', $email);

        return end($provider);
    }
}

if (! function_exists('validate_email')) {
    /**
     * Determine if the validation rule passes.
     */
    function validate_email($value): bool
    {
        $providers = config('disposable-email-providers');
        $provider = get_email_provider($value);

        return ! in_array($provider, $providers);
    }
}
