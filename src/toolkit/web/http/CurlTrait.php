<?php

    namespace yiitk\web\http;

    use yiitk\helpers\UrlHelper;

    /**
     * Trait CurlTrait
     */
    trait CurlTrait
    {
        /**
         * @param string $url
         * @param array  $allowedSchemes
         *
         * @return bool
         */
        public static function isValidUrl(string $url, array $allowedSchemes = ['http', 'https']): bool
        {
            return UrlHelper::validate($url, $allowedSchemes);
        }
    }
