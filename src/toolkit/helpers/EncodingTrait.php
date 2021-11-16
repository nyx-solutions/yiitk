<?php

    namespace yiitk\helpers;

    /**
     * Encoding Trait
     */
    trait EncodingTrait
    {
        /**
         * Verifica se uma string "parece" com UTF-8
         *
         * @param string $str
         *
         * @return bool
         *
         * @noinspection TypeUnsafeComparisonInspection
         */
        protected static function seemsUtf8($str)
        {
            static::mbstringBinarySafeEncoding();

            $length = strlen($str);

            static::resetMbstringEncoding();

            for ($i = 0; $i < $length; $i++) {
                $c = ord($str[$i]);
                if ($c < 0x80) {
                    $n = 0;
                } # 0bbbbbbb
                elseif (($c & 0xE0) == 0xC0) {
                    $n = 1;
                } # 110bbbbb
                elseif (($c & 0xF0) == 0xE0) {
                    $n = 2;
                } # 1110bbbb
                elseif (($c & 0xF8) == 0xF0) {
                    $n = 3;
                } # 11110bbb
                elseif (($c & 0xFC) == 0xF8) {
                    $n = 4;
                } # 111110bb
                elseif (($c & 0xFE) == 0xFC) {
                    $n = 5;
                } # 1111110b
                else {
                    return false;
                } # Does not match any model
                for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
                    if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80)) {
                        return false;
                    }
                }
            }

            return true;
        }

        /**
         * @param bool $reset
         *
         * @return void
         *
         * @noinspection ArrayPushMissUseInspection
         * @noinspection ComparisonOperandsOrderInspection
         */
        protected static function mbstringBinarySafeEncoding($reset = false)
        {
            static $encodings = [];
            static $overloaded = null;

            if ($overloaded === null) {
                $overloaded = function_exists('mb_internal_encoding');
            }

            if (false === $overloaded) {
                return;
            }

            if (!$reset) {
                $encoding = mb_internal_encoding();

                array_push($encodings, $encoding);

                mb_internal_encoding('ISO-8859-1');
            }

            if ($reset && $encodings) {
                $encoding = array_pop($encodings);
                mb_internal_encoding($encoding);
            }
        }

        /**
         * @return void
         */
        protected static function resetMbstringEncoding()
        {
            static::mbstringBinarySafeEncoding(true);
        }

        /**
         * @param string $string
         *
         * @return string
         *
         * @noinspection SubStrUsedAsArrayAccessInspection
         */
        public static function toASCII($string = '') {
            $convertedString = '';

            $length = strlen($string);

            for ($i = 0; $i < $length; $i++) {
                $convertedString .= '\\x'.dechex(ord(substr($string, $i, 1)));
            }

            return $convertedString;
        }
    }
