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

            Yii::setAlias('@pwdstrength', __DIR__);

            if (empty($this->i18n)) {
                $this->i18n = [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath'       => '@pwdstrength/messages'
                ];
            }

            Yii::$app->i18n->translations['kvpwdstrength'] = $this->i18n;

            $this->applyPreset();
            $this->checkParams();
            $this->setRuleMessages();
        }
    }
