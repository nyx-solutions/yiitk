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
        public string $thousands = '.';

        /**
         * @var string
         */
        public string $decimal = ',';

        /**
         * @var int
         */
        public int $precision = 2;

        //region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            $thousands = $this->thousands;
            $decimal   = $this->decimal;

            $this->addFilter(
                static function ($value) use ($thousands, $decimal) {
                    $value = trim((string)$value);

                    $value = str_replace([$thousands, $decimal], ['', '.'], $value);
                    $value = (float)preg_replace('/([^0-9.]+)/', '', (string)$value);

                    return $value;
                }
            );

            parent::init();
        }
        //endregion
    }
