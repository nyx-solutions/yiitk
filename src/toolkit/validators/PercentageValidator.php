<?php

    namespace yiitk\validators;

    use yii\helpers\Json;
    use yii\validators\NumberValidator;
    use yii\validators\ValidationAsset;

    /**
     * Class PercentageValidator
     */
    class PercentageValidator extends NumberValidator
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

        #region Validations
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function clientValidateAttribute($model, $attribute, $view)
        {
            ValidationAsset::register($view);

            $options = $this->getClientOptions($model, $attribute);

            return 'yii.validation.number(parseFloat(value.replace(/([^0-9\.]+)/gi, \'\')), messages, ' . Json::htmlEncode($options) . ');';

        }
        #endregion
    }
