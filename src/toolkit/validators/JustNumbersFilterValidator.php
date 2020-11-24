<?php

    namespace yiitk\validators;

    use yiitk\helpers\StringHelper;

    /**
     * Class JustNumbersFilterValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class JustNumbersFilterValidator extends FilterValidator
    {
        //region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            $this->addFilter(
                static function ($value) {
                    return StringHelper::justNumbers($value);
                }
            );

            parent::init();
        }
        //endregion
    }
