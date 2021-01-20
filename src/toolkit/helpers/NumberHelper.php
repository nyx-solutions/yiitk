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
         * @param float|null $amount
         * @param bool       $withPrefix
         *
         * @return string
         *
         * @see StringHelper::toMoney
         */
        public static function toBrazilianCurrency(?float $amount, bool $withPrefix = true): string
        {
            if ($amount === null) {
                $amount = 0.00;
            }

            return StringHelper::toBrazilianCurrency($amount, $withPrefix);
        }

        /**
         * @param float|null $amount
         * @param bool       $withPrefix
         *
         * @return string
         */
        public static function toPercentText(?float $amount, bool $withPrefix = true): string
        {
            if ($amount === null) {
                $amount = 0.00;
            }

            return number_format($amount, 2, '.', '').(($withPrefix) ? '%' : '');
        }

        /**
         * @param string|null $amount
         *
         * @return float
         */
        public static function percentToFloat(?string $amount): float
        {
            if (empty($amount)) {
                $amount = '0.00%';
            }

            return (float)preg_replace('/([^0-9.]+)/', '', $amount);
        }

        /**
         * @param string|null $amount
         *
         * @return float
         */
        public static function brazilianCurrencyToFloat(?string $amount): float
        {
            if (empty($amount)) {
                $amount = 'R$ 0,00';
            }

            $amount = str_replace(['.', ','], ['', '.'], $amount);
            $amount = (string)preg_replace('/([^0-9.]+)/', '', $amount);

            return (float)$amount;
        }

        /**
         * @param string|null $amount
         *
         * @return float
         */
        public static function toFloat(?string $amount): float
        {
            if (!empty($amount) && is_numeric($amount)) {
                return (float)$amount;
            }

            return 0.00;
        }
    }
