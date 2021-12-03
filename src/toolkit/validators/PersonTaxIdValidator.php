<?php

    namespace yiitk\validators;

    use JsonException;
    use Yii;
    use yii\validators\Validator;
    use yiitk\helpers\StringHelper;

    /**
     * Class PersonTaxIdValidator
     */
    class PersonTaxIdValidator extends Validator
    {
        #region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            $this->message = Yii::t('yiitk', 'The brazilian tax number is not valid.');
        }
        #endregion

        #region Validations
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function validateAttribute($model, $attribute)
        {
            if (!$this->validateTaxId($model->$attribute)) {
                $this->addError($model, $attribute, $this->message);
            }
        }

        /**
         * Validates a if a value is a valid CPF number.
         *
         * @param string|null $taxId CPF Number
         *
         * @return bool
         */
        public function validateTaxId(?string $taxId): bool
        {
            $taxId = (string)StringHelper::justNumbers((string)$taxId);

            $taxId = str_pad($taxId, 11, '0', STR_PAD_LEFT);

            if ($this->skipOnEmpty && empty($taxId)) {
                return true;
            }

            return self::isTaxIdValid($taxId);
        }

        /**
         * @param string $taxId
         *
         * @return bool
         */
        public static function isTaxIdValid(string $taxId): bool
        {
            $taxId = StringHelper::justNumbers($taxId);

            if (strlen($taxId) !== 11 || in_array($taxId, [
                    '00000000000',
                    '11111111111',
                    '22222222222',
                    '33333333333',
                    '44444444444',
                    '55555555555',
                    '66666666666',
                    '77777777777',
                    '88888888888',
                    '99999999999'
                ])
            ) {
                return false;
            }

            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $taxId[$c] * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                /** @noinspection TypeUnsafeComparisonInspection */
                if ($taxId[$c] != $d) {
                    return false;
                }
            }

            return true;
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        protected function validateValue($value)
        {
            if ($this->validateTaxId($value)) {
                return null;
            }

            return [$this->message, []];
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function clientValidateAttribute($model, $attribute, $view)
        {
            try {
                $message = json_encode($this->message, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $message = '';
            }

            $skipOnEmpty = (($this->skipOnEmpty) ? 'if(cpf == \'\') return true;' : '');

            return /** @lang TEXT */ <<<JS
if(typeof(validateCpfNumber) != 'function'){
	function validateCpfNumber(cpf){
		var sum, residual;

		sum = 0;

		cpf = cpf.replace(/([^0-9]{1,})/g, '');

		{$skipOnEmpty}

		if(cpf == '00000000000' || cpf == '11111111111' || cpf == '22222222222' || cpf == '33333333333' || cpf == '44444444444' || cpf == '55555555555' || cpf == '66666666666' || cpf == '77777777777' || cpf == '88888888888' || cpf == '99999999999') return false;

		for(var i = 1; i <= 9; i++) sum = sum + parseInt(cpf.substring(i - 1, i)) * (11 - i);

		residual = (sum * 10) % 11;

		if((residual == 10) || (residual == 11)) residual = 0;
		if(residual != parseInt(cpf.substring(9, 10))) return false;

		sum = 0;

		for(i = 1; i <= 10; i++) sum = sum + parseInt(cpf.substring(i-1, i)) * (12 - i);

		residual = (sum * 10) % 11;

		if((residual == 10) || (residual == 11)) residual = 0;
		if(residual != parseInt(cpf.substring(10, 11))) return false;

		return true;
	}
}

if(!validateCpfNumber(value)){
	messages.push($message);
}

JS;
        }
        #endregion
    }
