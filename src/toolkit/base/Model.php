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

        #region Events
        /**
         * @inheritdoc
         */
        public function afterValidate()
        {
            $errors = $this->getErrors();

            foreach ($errors as $error) {
                foreach ($error as $message) {
                    $this->addErrorMessage($message);
                }
            }

            parent::afterValidate();
        }
        #endregion
    }
