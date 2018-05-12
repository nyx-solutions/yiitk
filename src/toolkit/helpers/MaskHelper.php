<?php

    namespace yiitk\helpers;

    use Yii;

    /**
     * Class MaskHelper
     *
     * @package yiitk\helpers
     */
    class MaskHelper extends StringHelper
    {
        const MASK_PATTERN_CPF         = '###.###.###-###';
        const MASK_PATTERN_CNPJ        = '##.###.###/####-##';
        const MASK_PATTERN_ZIP_CODE    = '#####-###';
        const MASK_PATTERN_CREDIT_CARD = '#### #### #### ####';

        /**
         * Returns a string with a certain mask (using # as a pattern).
         *
         * @param $string  string
         * @param $mask    string
         * @param $onEmpty string
         *
         * @return string
         */
        public static function mask($string, $mask, $onEmpty = '')
        {
            $string = trim((string)$string);

            if (empty($string)) {
                return $onEmpty;
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
    }
