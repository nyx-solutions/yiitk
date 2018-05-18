<?php

    namespace yiitk\console\controllers;

    use yiitk\helpers\FileHelper;

    /**
     * Class AssetController
     */
    class AssetController extends \yii\console\controllers\AssetController
    {
        /**
         * @var array
         */
        public $paths = [];

        /**
         * Flush generated asset files defined in the $paths param.
         *
         * @throws \yii\base\ErrorException
         */
        public function actionFlush()
        {
            if (is_array($this->paths)) {
                foreach ($this->paths as $path) {
                    $path = \Yii::getAlias($path);

                    if (is_dir($path)) {
                        foreach (new \DirectoryIterator($path) as $fileInfo) {
                            if($fileInfo->isDot()) {
                                continue;
                            }

                            FileHelper::removeDirectory($fileInfo->getPathname());
                        }
                    }
                }
            }
        }
    }
