<?php

    namespace yiitk\helpers;

    /**
     * Class MaskHelper
     */
    class MaskHelper extends StringHelper
    {
        /**
         * @var array
         */
        protected static array $patters = [
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
        public static function mask(string $string, string $mask, $empty = ''): string
        {
            if (isset(static::$patters[strtolower($mask)])) {
                $mask = static::$patters[strtolower($mask)];
            }

            $string = trim($string);

            if (empty($string)) {
                return $empty;
            }

            $maskared = '';

            $k = 0;

            for ($i = 0; $i <= (strlen($mask) - 1); $i++) {
                if ($mask[$i] === '#') {
                    if (isset($string[$k])) {
                        $maskared .= $string[$k++];
                    }
                } elseif (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }

            return $maskared;
        }

        /**
         * @param string $basePhone
         *
         * @return string
         */
        public static function maskPhone(string $basePhone): string
        {
            $phone = (int)static::justNumbers($basePhone);
            $phone = (string)$phone;

            if (strlen($phone) === 11) {
                return static::mask($phone, '(##) #####-####');
            }

            if (strlen($phone) === 10) {
                return static::mask($phone, '(##) ####-####');
            }

            return '';
        }
    }
