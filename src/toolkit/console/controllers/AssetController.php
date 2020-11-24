<?php

    namespace yiitk\console\controllers;

    use DirectoryIterator;
    use Yii;
    use yii\base\ErrorException;
    use yiitk\helpers\FileHelper;

    /**
     * Class AssetController
     *
     * @noinspection ContractViolationInspection
     */
    class AssetController extends \yii\console\controllers\AssetController
    {
        /**
         * @var array
         */
        public array $paths = [];

        /**
         * Flush generated asset files defined in the $paths param.
         *
         * @throws ErrorException
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function actionFlush()
        {
            if (is_array($this->paths)) {
                foreach ($this->paths as $path) {
                    $path = Yii::getAlias($path);

                    if (is_dir($path)) {
                        foreach (new DirectoryIterator($path) as $fileInfo) {
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
