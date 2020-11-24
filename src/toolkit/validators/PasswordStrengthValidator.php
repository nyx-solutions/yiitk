<?php

    namespace yiitk\validators;

    use kartik\password\StrengthValidator;
    use Yii;
    use yii\i18n\PhpMessageSource;

    /**
     * Class PasswordStrengthValidator
     *
     * @package yiitk\validators
     */
    class PasswordStrengthValidator extends StrengthValidator
    {
        //region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            Yii::setAlias('@pwdstrength', __DIR__);

            if (empty($this->i18n)) {
                $this->i18n = [
                    'class'          => PhpMessageSource::class,
                    'sourceLanguage' => 'en-US',
                    'basePath'       => '@pwdstrength/messages'
                ];
            }

            Yii::$app->i18n->translations['kvpwdstrength'] = $this->i18n;

            $this->encoding = Yii::$app->charset;

            parent::init();
        }
        //endregion
    }
