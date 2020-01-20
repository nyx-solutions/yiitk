<?php

    namespace yiitk\validators;

    use yii\validators\RegularExpressionValidator;

    /**
     * Class UsernameValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class UsernameValidator extends RegularExpressionValidator
    {
        /**
         * @var string
         */
        public $pattern = '/^([a-z.-]+)$/';
    }
