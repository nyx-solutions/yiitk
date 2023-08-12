<?php

    namespace yiitk\validators;

    use yii\helpers\Json;
    use yii\validators\NumberValidator;
    use yii\validators\ValidationAsset;

    /**
     * Class BrazilianMoneyValidator
     */
    class BrazilianMoneyValidator extends NumberValidator
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

            $this->integerOnly = false;
        }
        #endregion

        #region Client Validation
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function clientValidateAttribute($model, $attribute, $view)
        {
            ValidationAsset::register($view);

            $options = $this->getClientOptions($model, $attribute);

            return 'yii.validation.number(parseFloat(value.replace(/(\.)/gi, \'\').replace(/(\,)/gi, \'.\').replace(/([^0-9\.]+)/gi, \'\')), messages, ' . Json::htmlEncode($options) . ');';
        }
        #endregion
    }
