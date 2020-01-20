<?php

    namespace yiitk\console\controllers;

    /**
     * Manages application migrations.
     */
    class MigrateController extends \yii\console\controllers\MigrateController
    {
        /**
         * @var array
         */
        public $generatorTemplateFiles = [];

        /**
         * {@inheritdoc}
         */
        public function init()
        {
            \Yii::setAlias('@yiitk/migrations/controllers', dirname(__DIR__));

            $this->templateFile = '@yiitk/migrations/controllers/views/migration.php';

            parent::init();
        }

        /**
         * @inheritdoc
         */
        protected function generateMigrationSourceCode($params)
        {
            $extendedParams = [];
            $migrationName  = $params['name'];

            $extendedParams['migrationSubview'] = '_create';

            if (preg_match('/^update_.*$/', $migrationName)) {
                $extendedParams['migrationSubview'] = '_update';
            } elseif (preg_match('/^drop_.*$/', $migrationName)) {
                $extendedParams['migrationSubview'] = '_drop';
            }

            $extendedParams['tableName']       = preg_replace('/^(create|update|drop)_(.*)$/', '$2', $migrationName);
            $extendedParams['tableSimpleName'] = preg_replace('/^(int)_(.*)$/', '$2', $extendedParams['tableName']);

            return $this->renderFile(\Yii::getAlias($this->templateFile), array_merge($params, $extendedParams));
        }
    }
