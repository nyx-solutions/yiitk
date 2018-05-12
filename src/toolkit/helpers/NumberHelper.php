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
    }
