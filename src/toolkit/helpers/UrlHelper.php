<?php

    namespace yiitk\helpers;

    use yii\helpers\Url;

    /**
     * Class UrlHelper
     */
    class UrlHelper extends Url
    {
        /**
         * @param string $url
         * @param array  $allowedSchemes
         *
         * @return bool
         */
        public static function validate(string $url, array $allowedSchemes = ['http', 'https']): bool
        {
            /** @noinspection BypassedUrlValidationInspection */
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $parts = parse_url($url);

                $scheme = strtolower(($parts['scheme'] ?? ''));
                $host   = strtolower(($parts['host'] ?? ''));

                return (in_array($scheme, $allowedSchemes, true) && !empty($host));
            }

            return false;
        }
    }
