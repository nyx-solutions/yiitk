<?php

    namespace yiitk\validators;

    /**
     * Trait ValidatorsTrait
     */
    trait ValidatorsTrait
    {
        use TaxIdValidatorTrait;

        /**
         * Adds a new error to the specified attribute.
         *
         * @param string $attribute attribute name
         * @param string $error new error message
         *
         * @noinspection PhpMissingParamTypeInspection
         */
        abstract public function addError($attribute, $error = '');

        /**
         * @param string $attribute
         */
        public function validatePhoneNumber(string $attribute): void
        {
            if (!empty($this->$attribute) && !preg_match('/^\(\d{2}\) \d{4,5}-\d{4,5}$/', $this->$attribute)) {
                $this->addError($attribute, 'O formato do número de telefone informado não é válido.');
            }
        }
    }
