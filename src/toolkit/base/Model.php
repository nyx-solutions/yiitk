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
        use EnumTrait, FlashMessagesTrait;

        /**
         * @var bool
         */
        protected $enableFlashMessages = true;

        /**
         * @var bool
         */
        protected $addModelsErrosToFlashMessages = false;

        #region Events
        /**
         * @inheritdoc
         */
        public function afterValidate()
        {
            if ($this->enableFlashMessages && $this->addModelsErrosToFlashMessages) {
                $errors = $this->getErrors();

                foreach ($errors as $error) {
                    foreach ($error as $message) {
                        $this->addErrorMessage($message);
                    }
                }
            }

            parent::afterValidate();
        }
        #endregion

        #region Fields
        /**
         * @inheritdoc
         */
        public function fields()
        {
            return $this->parseFields(parent::fields());
        }
        #endregion
    }
