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
        #region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            $this->addFilter(
                static function ($value) {
                    return MaskHelper::maskPhone($value);
                }
            );

            parent::init();
        }
        #endregion
    }
