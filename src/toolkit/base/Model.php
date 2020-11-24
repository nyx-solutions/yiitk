<?php

    namespace yiitk\base;

    use yiitk\enum\base\EnumTrait;
    use yiitk\web\FlashMessagesTrait;

    /**
     * Class Model
     *
     * @package yiitk\base
     */
    class Model extends \yii\base\Model
    {
        use EnumTrait;
        use FlashMessagesTrait;

        /**
         * @var bool
         */
        protected bool $addModelsErrosToFlashMessages = false;

        //region Events
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function afterValidate()
        {
            if ($this->enableFlashMessages && $this->addModelsErrosToFlashMessages) {
                foreach ($this->getErrors() as $error) {
                    foreach ($error as $message) {
                        $this->addErrorMessage($message);
                    }
                }
            }

            parent::afterValidate();
        }
        //endregion

        //region Fields
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function fields()
        {
            return $this->parseFields(parent::fields());
        }
        //endregion
    }
