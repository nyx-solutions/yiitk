<?php

    namespace yiitk\helpers;

    /**
     * Class CryptHelper
     *
     * @package yiitk\helpers
     */
    class CryptHelper extends StringHelper
    {
        const SECRET = '348090af7ef0621b1fc980719b890596ed6a22be6ee5137de4ded23b8867a598f9d7e7d5fec22b9f3647ff42b148b9ee98003aab116206816ec9827b9915b56c';

        /**
         * @param $string
         *
         * @return string
         */
        public static function encrypt($string)
        {
            $key = pack('H*', self::SECRET);
            $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);

            return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $iv);
        }

        /**
         * @param $string
         *
         * @return string
         */
        public static function decrypt($string)
        {
            $key = pack('H*', self::SECRET);
            $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);

            return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $iv);
        }
    }
