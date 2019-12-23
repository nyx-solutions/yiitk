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
        /**
         * @inheritdoc
         */
        public function init()
        {
            $this->addFilter(
                function ($value) {
                    return StringHelper::justNumbers($value);
                }
            );

            parent::init();
        }
    }
