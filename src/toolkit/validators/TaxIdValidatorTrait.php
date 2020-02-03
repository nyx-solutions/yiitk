<?php

    namespace yiitk\validators;

    use yiitk\helpers\StringHelper;

    /**
     * Trait TaxIdValidatorTrait
     *
     * @package common\components\validators
     */
    trait TaxIdValidatorTrait
    {
        /**
         * Adds a new error to the specified attribute.
         *
         * @param string $attribute attribute name
         * @param string $error new error message
         */
        abstract public function addError($attribute, $error = '');

        /**
         * @param string $attribute the attribute currently being validated
         */
        public function validateTaxId($attribute)
        {
            if (!$this->_validateTaxId($this->$attribute)) {
                $type = (($this->_isPersonsTaxId($this->$attribute)) ? 'CPF' : 'CNPJ');

                $this->addError($attribute, "O número de documento ({$type}) informado não é válido.");
            }
        }

        /**
         * @param $number
         *
         * @return bool
         */
        private function _isPersonsTaxId($number)
        {
            $number = StringHelper::justNumbers($number);

            return (strlen($number) <= 11);
        }

        /**
         * @param string $number
         *
         * @return bool
         */
        private function _validateTaxId($number)
        {
            $number = (string)StringHelper::justNumbers($number);

            if ($this->_isPersonsTaxId($number)) {
                return $this->_validatePersonsTaxId($number);
            } else {
                return $this->_validateCompaniesTaxId($number);
            }
        }

        /**
         * @param string $number
         *
         * @return bool
         */
        private function _validatePersonsTaxId($number)
        {
            $number = (int)StringHelper::justNumbers($number);

            if(empty($number)) {
                return false;
            }

            $number = str_pad((string)$number, 11, '0', STR_PAD_LEFT);

            if (strlen($number) != 11) {
                return false;
            } else if ($number == '00000000000' ||
                $number == '11111111111' ||
                $number == '22222222222' ||
                $number == '33333333333' ||
                $number == '44444444444' ||
                $number == '55555555555' ||
                $number == '66666666666' ||
                $number == '77777777777' ||
                $number == '88888888888' ||
                $number == '99999999999') {

                return false;
            } else {
                for ($t = 9; $t < 11; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $number[$c] * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($number[$c] != $d) {
                        return false;
                    }
                }

                return true;
            }
        }

        /**
         * @param string $number
         *
         * @return bool
         */
        private function _validateCompaniesTaxId($number)
        {
            $number = (int)StringHelper::justNumbers($number);

            if(empty($number)) {
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

            if ($number[12] == $d1 && $number[13] == $d2) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * @param string $number
         *
         * @return string
         */
        protected function formatTaxId($number)
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
    }
