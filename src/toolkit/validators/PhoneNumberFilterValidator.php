<?php

    namespace yiitk\validators;

    use yiitk\helpers\MaskHelper;

    /**
     * Class PhoneNumberFilterValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class PhoneNumberFilterValidator extends FilterValidator
    {
        /**
         * @inheritdoc
         */
        public function init()
        {
            $this->addFilter(
                function ($value) {
                    return MaskHelper::maskPhone($value);
                }
            );

            parent::init();
        }
    }
