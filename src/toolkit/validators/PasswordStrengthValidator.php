<?php

    namespace yiitk\validators;

    use kartik\password\StrengthValidator;
    use Yii;

    /**
     * Class PasswordStrengthValidator
     *
     * @package yiitk\validators
     */
    class PasswordStrengthValidator extends StrengthValidator
    {
        /**
         * {@inheritdoc}
         */
        public function init()
        {
            $this->encoding = Yii::$app->charset;

            $this->applyPreset();
            $this->checkParams();
            $this->setRuleMessages();
        }
    }
