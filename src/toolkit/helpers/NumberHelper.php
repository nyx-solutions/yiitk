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
        public static function toMoney($amount, $withPrefix = true)
        {
            return StringHelper::toMoney($amount, $withPrefix);
        }

        /**
         * @param float $amount
         *
         * @return string
         */
        public static function toPercentText($amount)
        {
            return static::toMoney($amount, false).'%';
        }
    }
