<?php

    namespace yiitk\web\http;

    /**
     * Class Curl
     *
     * @package yiitk\request
     */
    class MultiCurl extends \Curl\MultiCurl
    {
        /**
         * @param string $url
         * @param array  $allowedSchemes
         *
         * @return bool
         */
        public static function isValidUrl($url, $allowedSchemes = ['http', 'https'])
        {
            return Curl::isValidUrl($url, $allowedSchemes);
        }
    }
