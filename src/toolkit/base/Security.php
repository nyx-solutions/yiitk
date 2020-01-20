<?php

    namespace yiitk\base;

    /**
     * Class Security
     */
    class Security extends \yii\base\Security
    {
        /**
         * @var string
         */
        public $secretKey = null;

        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            if (!empty($this->secretKey)) {
                $this->secretKey = sha1($this->secretKey);
            }
        }
    }
