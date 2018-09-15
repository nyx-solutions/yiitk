<?php

    namespace yiitk\web;

    /**
     * Class Controller
     */
    class Controller extends \yii\web\Controller
    {
        use FlashMessagesTrait;

        /**
         * @var bool
         */
        protected $enableFlashMessages = true;
    }
