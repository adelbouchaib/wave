<?php

use Carbon\Carbon;
use Illuminate\Support\Str;


if (!function_exists('isTextArabic')) {
    /**
     * Get the human-readable time difference between the current time and a given timestamp.
     *
     * @param string $text
     * @return boolean
     */
    function isTextArabic($text)
    {
         // Get the first character of the string
         $firstChar = mb_substr($text, 0, 1);

         // Check if the first character is in the Arabic range
         return preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u', $firstChar);
    }
}


if (!function_exists('cleanArabicText')) {
    /**
     * Clean consecutive question marks (??) in a string, replacing them with a single question mark.
     *
     * @param string $text
     * @return string
     */
    function cleanArabicText($text)
    {
        // Convert newlines to <br> tags
        $inputString = nl2br(e($text));

        // Replace consecutive question marks with a single one
        return preg_replace('/(\?){2,}/', '', $inputString);
    }
}
