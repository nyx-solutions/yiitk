<?php

    namespace yiitk;

    /**
     * Class Module
     *
     * @package yiitk
     */
    class Module extends \yii\base\Module
    {
        /**
         * {@inheritdoc}
         */
        public function init()
        {
            \Yii::setAlias('@yiitk', __DIR__);

            parent::init();
        }
    }
