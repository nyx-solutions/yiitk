<?php

    namespace yiitk\helpers;

    /**
     * Class NumberHelper
     *
     * @package yiitk\helpers
     */
    class NumberHelper
    {
        /**
         * Recebe uma string formatada e retorna apenas os seus números.
         *
         * @param string $content
         *
         * @return string
         *
         * @see StringHelper::justNumbers
         */
        public static function justNumbers($content = '')
        {
            return StringHelper::justNumbers($content);
        }

        /**
         * @param int      $value
         * @param bool|int $upper
         *
         * @return string
         *
         * @see StringHelper::toSpelled
         */
        public static function toSpelled($value = 0, $upper = false)
        {
            return StringHelper::toSpelledNumber($value, $upper);
        }

        /**
         * @param float $amount
         * @param bool  $withPrefix
         *
         * @return string
         *
         * @see StringHelper::toMoney
         */
        public static function toBrazilianCurrency($amount, $withPrefix = true)
        {
            return StringHelper::toBrazilianCurrency($amount, $withPrefix);
        }

        /**
         * @param float $amount
         * @param bool  $withPrefix
         *
         * @return string
         */
        public static function toPercentText($amount, $withPrefix = true)
        {
            return number_format((float)$amount, 2, '.', '').(($withPrefix) ? '%' : '');
        }

        /**
         * @param string $amount
         *
         * @return float
         */
        public static function percentToFloat($amount)
        {
            return (float)preg_replace('/([^0-9\.]+)/', '', (string)$amount);
        }

        /**
         * @param string $amount
         *
         * @return float
         */
        public static function brazilianCurrencyToFloat($amount)
        {
            $amount = str_replace('.', '', (string)$amount);
            $amount = str_replace(',', '.', $amount);
            $amount = preg_replace('/([^0-9\.]+)/', '', $amount);

            return (float)$amount;
        }

        /**
         * @param string $amount
         *
         * @return float
         */
        public static function toFloat($amount)
        {
            if (!empty($amount) && is_numeric($amount)) {
                return (float)$amount;
            }

            return 0.00;
        }
    }
