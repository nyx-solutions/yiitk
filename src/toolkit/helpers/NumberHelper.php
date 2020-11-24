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
        public static function justNumbers(string $content = ''): string
        {
            return StringHelper::justNumbers($content);
        }

        /**
         * @param int  $value
         * @param bool $upper
         *
         * @return string
         *
         * @see StringHelper::toSpelled
         */
        public static function toSpelled(int $value = 0, bool $upper = false): string
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
        public static function toBrazilianCurrency(float $amount, bool $withPrefix = true): string
        {
            return StringHelper::toBrazilianCurrency($amount, $withPrefix);
        }

        /**
         * @param float $amount
         * @param bool  $withPrefix
         *
         * @return string
         */
        public static function toPercentText(float $amount, bool $withPrefix = true): string
        {
            return number_format($amount, 2, '.', '').(($withPrefix) ? '%' : '');
        }

        /**
         * @param string $amount
         *
         * @return float
         */
        public static function percentToFloat(string $amount): float
        {
            return (float)preg_replace('/([^0-9.]+)/', '', $amount);
        }

        /**
         * @param string $amount
         *
         * @return float
         */
        public static function brazilianCurrencyToFloat(string $amount): float
        {
            $amount = str_replace(['.', ','], ['', '.'], $amount);
            $amount = preg_replace('/([^0-9.]+)/', '', $amount);

            return (float)$amount;
        }

        /**
         * @param string $amount
         *
         * @return float
         */
        public static function toFloat(string $amount): float
        {
            if (!empty($amount) && is_numeric($amount)) {
                return (float)$amount;
            }

            return 0.00;
        }
    }
