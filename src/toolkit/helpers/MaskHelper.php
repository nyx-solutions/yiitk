<?php

    namespace yiitk\helpers;

    /**
     * Class MaskHelper
     *
     * @package yiitk\helpers
     */
    class MaskHelper extends StringHelper
    {
        /**
         * @var array
         */
        protected static $patters = [
            'cpf'            => '###.###.###-###',
            'person-tax-id'  => '###.###.###-###',
            'cnpj'           => '##.###.###/####-##',
            'company-tax-id' => '##.###.###/####-##',
            'zipcode'        => '#####-###',
            'credit-card'    => '#### #### #### ####',
        ];

        /**
         * Returns a string with a certain mask (using # as a pattern).
         *
         * @param string $string
         * @param string $mask
         * @param string $empty
         *
         * @return string
         */
        public static function mask($string, $mask, $empty = '')
        {
            if (isset(static::$patters[strtolower($mask)])) {
                $mask = static::$patters[strtolower($mask)];
            }

            $string = trim((string)$string);

            if (empty($string)) {
                return $empty;
            }

            $maskared = '';

            $k = 0;

            for ($i = 0; $i <= (strlen($mask) - 1); $i++) {
                if ($mask[$i] == '#') {
                    if (isset($string[$k])) {
                        $maskared .= $string[$k++];
                    }
                } else {
                    if (isset($mask[$i])) {
                        $maskared .= $mask[$i];
                    }
                }
            }

            return $maskared;
        }

        /**
         * @param string $phone
         *
         * @return string
         */
        public static function maskPhone($phone)
        {
            $phone = (int)static::justNumbers($phone);
            $phone = (string)$phone;

            if (strlen($phone) === 11) {
                return static::mask($phone, '(##) #####-####');
            } elseif (strlen($phone) === 10) {
                return static::mask($phone, '(##) ####-####');
            }

            return '';
        }
    }
