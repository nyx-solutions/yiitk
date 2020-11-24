<?php

    /**
     * @noinspection PhpMissingFieldTypeInspection
     */

    namespace yiitk\validators;

    use yii\validators\RegularExpressionValidator;

    /**
     * Class PhoneNumberValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class PhoneNumberValidator extends RegularExpressionValidator
    {
        /**
         * @var string
         */
        public $pattern = '/^\(\d{2}\)\ \d{4,5}\-\d{4,5}$/';
    }
