<?php

    namespace yiitk\base;

    /**
     * Class Security
     */
    class Security extends \yii\base\Security
    {
        /**
         * @var string|null
         */
        public ?string $secretKey = null;

        //region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            if (!empty($this->secretKey)) {
                $this->secretKey = sha1($this->secretKey);
            }
        }
        //endregion
    }
