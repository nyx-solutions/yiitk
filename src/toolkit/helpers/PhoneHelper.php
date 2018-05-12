<?php

    namespace yiitk\helpers;

    /**
     * Class PhoneHelper
     *
     * @package yiitk\helpers
     */
    class PhoneHelper extends StringHelper
    {
        /**
         * @param string $phone
         *
         * @return string
         */
        public static function formatPhoneNumber($phone)
        {
            $phone = preg_replace('/\D/', '', $phone);

            if (preg_match('/(\d{2})+9?(\d{8})+/', $phone)) {
                return "55{$phone}";
            }

            return '';
        }

        /**
         * @param string $phone
         *
         * @return integer
         */
        public static function isValidPhoneNumber($phone)
        {
            $phone = preg_replace("/\D/", '', $phone);

            return preg_match('/(\d{2})+9?(\d{8})+/', $phone);
        }
    }
