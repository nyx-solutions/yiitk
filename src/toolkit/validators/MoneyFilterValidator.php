<?php

    namespace yiitk\validators;

    /**
     * Class MoneyFilterValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class MoneyFilterValidator extends FilterValidator
    {
        /**
         * @var string
         */
        public $thousands = '.';

        /**
         * @var string
         */
        public $decimal = ',';

        /**
         * @var integer
         */
        public $precision = 2;

        /**
         * @inheritdoc
         */
        public function init()
        {
            $thousands = $this->thousands;
            $decimal   = $this->decimal;

            $this->addFilter(
                function ($value) use ($thousands, $decimal) {
                    $value = trim((string)$value);

                    $value = str_replace($thousands, '', $value);
                    $value = str_replace($decimal, '.', $value);
                    $value = (float)preg_replace('/([^0-9.]+)/', '', (string)$value);

                    return $value;
                }
            );

            parent::init();
        }
    }
