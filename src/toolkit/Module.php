<?php

    namespace yiitk;

    use yii\i18n\PhpMessageSource;

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
            \Yii::setAlias('@yiitk/messages', dirname(__DIR__).'/messages');

            if (!isset(\Yii::$app->i18n->translations['yiitk'])) {
                \Yii::$app->i18n->translations['yiitk'] = [
                    'class'          => PhpMessageSource::class,
                    'sourceLanguage' => 'en-US',
                    'basePath'       => '@yiitk/messages',
                    'fileMap'        => ['yiitk' => 'yiitk.php']
                ];
            }

            parent::init();
        }
    }
