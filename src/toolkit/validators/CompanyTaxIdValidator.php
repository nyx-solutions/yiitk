<?php

    namespace yiitk\validators;

    use JsonException;
    use Yii;
    use yiitk\helpers\StringHelper;
    use yii\validators\Validator;

    /**
     * Class CompanyTaxIdValidator
     */
    class CompanyTaxIdValidator extends Validator
    {
        //region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            $this->message = Yii::t('yiitk', 'The brazilian company tax number is not valid.');
        }
        //endregion

        //region Validations
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
         * @param $taxId string|null CNPJ Number
         *
         * @return bool
         */
        private function validateTaxId(?string $taxId): bool
        {
            $taxId = (string)StringHelper::justNumbers($taxId);

            if ($this->skipOnEmpty && empty($taxId)) {
                return true;
            }

            $taxId = str_pad($taxId, 14, '0', STR_PAD_LEFT);

            /** @noinspection NotOptimalIfConditionsInspection */
            if (strlen($taxId) < 14 || strlen($taxId) > 14) {
                return false;
            }

            for ($i = 0, $j = 5, $sum = 0; $i < 12; $i++) {
                $sum += $taxId[$i] * $j;

                $j = ($j === 2) ? 9 : $j - 1;
            }

            $residual = $sum % 11;

            if ($taxId[12] !== ($residual < 2 ? 0 : 11 - $residual)) {
                return false;
            }

            for ($i = 0, $j = 6, $sum = 0; $i < 13; $i++) {
                $sum += $taxId[$i] * $j;

                $j = ($j === 2) ? 9 : $j - 1;
            }

            $residual = ($sum % 11);

            return $taxId[13] === ($residual < 2 ? 0 : 11 - $residual);
        }

        /**
         * @inheritdoc
         *
         * @noinspection PhpOverridingMethodVisibilityInspection
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function validateValue($value)
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
            } catch (JsonException $e) {
                $message = '';
            }

            $skipOnEmpty = (($this->skipOnEmpty) ? 'if(cnpj == \'\') return true;' : '');

            return /** @lang TEXT */<<<JS
if(typeof(validateCnpjNumber) != 'function'){
	function validateCnpjNumber(cnpj){
		cnpj = cnpj.replace(/([^0-9]{1,})/g, '');

		{$skipOnEmpty}

		var i, c = cnpj.substr(0,12), dv = cnpj.substr(12,2), d1 = 0;

		for(i = 0; i < 12; i++){
			d1 += c.charAt(11-i)*(2+(i % 8));
		}

		if(d1 == 0) return false;

		d1 = 11 - (d1 % 11);

		if(d1 > 9) d1 = 0;

		if(dv.charAt(0) != d1) return false;

		d1 *= 2;

		for(i = 0; i < 12; i++){
			d1 += c.charAt(11-i)*(2+((i+1) % 8));
		}

		d1 = 11 - (d1 % 11);

		if(d1 > 9) d1 = 0;
		if(dv.charAt(1) != d1) return false;

		return true;
	}
}

if(!validateCnpjNumber(value)){
	messages.push($message);
}

JS;
        }
        //endregion
    }
