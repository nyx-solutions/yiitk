<?php

    namespace yiitk\validators;

    use JsonException;
    use Yii;
    use yii\validators\Validator;
    use yiitk\base\Model;

    /**
     * Class OneWordValidator
     */
    class OneWordValidator extends Validator
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

            $this->message = Yii::t('yiitk', 'The field "{attribute}" must have only one word (without numbers, special chars or spaces).');
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
            /** @var Model $model */
            if (!$this->validateWord($model->$attribute)) {
                $this->addError($model, $attribute, $this->getMessage($model->getAttributeLabel($attribute)));
            }
        }

        /**
         * Validates if a value is a valid single word.
         *
         * @param $word string Word
         *
         * @return bool
         */
        private function validateWord(string $word): bool
        {
            if ($this->skipOnEmpty && empty($word)) {
                return true;
            }

            if (preg_match('/( )/', $word)) {
                return false;
            }
            if (preg_match('/([\d]+)/', $word)) {
                return false;
            }

            $specialChars = ['"', '\'', '!', '@', '#', '$', '%', '¨', '&', '*', '(', ')', '_', '-', '+', '=', '§', 'ª', 'º', '{', '}', '[', ']', '?', '/', '\\', ';', ':', '.', ',', '<', '>', '|', '´', '`', '^', '~'];

            foreach ($specialChars as $specialChar) {
                if (str_contains($word, $specialChar)) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Gets the filtered message.
         *
         * @param $attribute string Attribute name
         *
         * @return string Message
         */
        protected function getMessage(string $attribute): string
        {
            return (string)preg_replace('/{attribute}/', $attribute, $this->message);
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        protected function validateValue($value)
        {
            if ($this->validateWord($value)) {
                return null;
            }

            return ['O valor deve ser uma única palavra (sem números, caracteres especiais ou espaços).', []];
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function clientValidateAttribute($model, $attribute, $view)
        {
            try {
                $message = json_encode($this->getMessage($model->getAttributeLabel($attribute)), JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                $message = '';
            }

            $skipOnEmpty = (($this->skipOnEmpty) ? 'if(word == \'\') return true;' : '');

            return /** @lang TEXT */ <<<JS
if(typeof(validateOneWord) != 'function'){
	function validateOneWord(word){
		{$skipOnEmpty}

		if(word.match(/(\ )/g)) return false;
		if(word.match(/([0-9]{1,})/g)) return false;

		var specialChars = ['"', '\'', '!', '@', '#', '$', '%', '¨', '&', '*', '(', ')', '_', '-', '+', '=', '§', 'ª', 'º', '{', '}', '[', ']', '?', '/', '\\\', ';', ':', '.', ',', '<', '>', '|', '´', '`', '^', '~'];

		for(var i = 0; i < specialChars.length; i++){
			if(word.indexOf(specialChars[i]) !== -1) return false;
		}

		return true;
	}
}

if(!validateOneWord(value)){
	messages.push($message);
}

JS;
        }
        #endregion
    }
