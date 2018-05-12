<?php

    namespace yiitk\helpers;

    /**
     * Class CurlHelper
     *
     * @package yiitk\helpers
     */
    class CurlHelper
    {
        /**
         * @param string $url
         * @param array  $allowedSchemes
         *
         * @return bool
         */
        public static function isValidUrl($url, $allowedSchemes = ['http', 'https'])
        {
            $validUrl = !(filter_var($url, FILTER_VALIDATE_URL) === false);

            $scheme = parse_url($url, PHP_URL_SCHEME);

            return ($validUrl && in_array($scheme, $allowedSchemes, true));
        }
    }
