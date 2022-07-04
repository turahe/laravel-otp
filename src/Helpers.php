<?php

if (! function_exists('validation_number')) {
    /**
     * Validate phone number
     *
     * @param $number
     * @return bool
     * @throws \libphonenumber\NumberParseException
     */
    function validation_number($number): bool
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
     * @param $number
     * @return string
     * @throws \libphonenumber\NumberParseException
     */
    function format_phone($number): string
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
