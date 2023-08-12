<?php

    namespace yiitk\validators;

    use yiitk\helpers\StringHelper;

    /**
     * Trait TaxIdValidatorTrait
     */
    trait TaxIdValidatorTrait
    {
        /**
         * Adds a new error to the specified attribute.
         *
         * @param string $attribute attribute name
         * @param string $error new error message
         *
         * @noinspection PhpMissingParamTypeInspection
         */
        abstract public function addError($attribute, $error = '');

        #region Validations
        /**
         * @param string $attribute the attribute currently being validated
         */
        public function validateTaxId(string $attribute): void
        {
            if (!$this->_validateTaxId($this->$attribute)) {
                $type = (($this->_isPersonsTaxId($this->$attribute)) ? 'CPF' : 'CNPJ');

                $this->addError($attribute, "O número de documento ({$type}) informado não é válido.");
            }
        }

        /**
         * @param string $number
         *
         * @return bool
         */
        private function _isPersonsTaxId(string $number): bool
        {
            $number = StringHelper::justNumbers($number);

            return (strlen($number) <= 11);
        }

        /**
         * @param string $number
         *
         * @return bool
         */
        private function _validateTaxId(string $number): bool
        {
            $number = (string)StringHelper::justNumbers($number);

            if ($this->_isPersonsTaxId($number)) {
                return $this->_validatePersonsTaxId($number);
            }

            return $this->_validateCompaniesTaxId($number);
        }

        /**
         * @param string|null $taxIdNumber
         *
         * @return bool
         */
        private function _validatePersonsTaxId(?string $taxIdNumber): bool
        {
            $number = (int)StringHelper::justNumbers($taxIdNumber);

            if (empty($number)) {
                return false;
            }

            $number = str_pad((string)$number, 11, '0', STR_PAD_LEFT);

            if (strlen($number) !== 11) {
                return false;
            }

            if (in_array($number, ['00000000000','11111111111','22222222222','33333333333','44444444444','55555555555','66666666666','77777777777','88888888888','99999999999'], true)) {
                return false;
            }

            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $number[$c] * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ((int)$number[$c] !== $d) {
                    return false;
                }
            }

            return true;
        }

        /**
         * @param string|null $taxIdNumber
         *
         * @return bool
         */
        private function _validateCompaniesTaxId(?string $taxIdNumber): bool
        {
            $number = (int)StringHelper::justNumbers($taxIdNumber);

            if (empty($number)) {
                return false;
            }

            $number = str_pad((string)$number, 14, '0', STR_PAD_LEFT);

            $soma = 0;
            $soma += ($number[0] * 5);
            $soma += ($number[1] * 4);
            $soma += ($number[2] * 3);
            $soma += ($number[3] * 2);
            $soma += ($number[4] * 9);
            $soma += ($number[5] * 8);
            $soma += ($number[6] * 7);
            $soma += ($number[7] * 6);
            $soma += ($number[8] * 5);
            $soma += ($number[9] * 4);
            $soma += ($number[10] * 3);
            $soma += ($number[11] * 2);

            $d1 = $soma % 11;
            $d1 = $d1 < 2 ? 0 : 11 - $d1;

            $soma = 0;
            $soma += ($number[0] * 6);
            $soma += ($number[1] * 5);
            $soma += ($number[2] * 4);
            $soma += ($number[3] * 3);
            $soma += ($number[4] * 2);
            $soma += ($number[5] * 9);
            $soma += ($number[6] * 8);
            $soma += ($number[7] * 7);
            $soma += ($number[8] * 6);
            $soma += ($number[9] * 5);
            $soma += ($number[10] * 4);
            $soma += ($number[11] * 3);
            $soma += ($number[12] * 2);

            $d2 = $soma % 11;
            $d2 = $d2 < 2 ? 0 : 11 - $d2;

            return (int)$number[12] === $d1 && (int)$number[13] === $d2;
        }

        /**
         * @param string $number
         *
         * @return string
         */
        protected function formatTaxId(string $number): string
        {
            $number = StringHelper::justNumbers($number);

            if ($this->_isPersonsTaxId($number)) {
                $number = str_pad($number, 11, '0', STR_PAD_LEFT);
                $number = preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $number);
            } else {
                $number = str_pad($number, 14, '0', STR_PAD_LEFT);
                $number = preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $number);
            }

            return (string)$number;
        }
        #endregion
    }
