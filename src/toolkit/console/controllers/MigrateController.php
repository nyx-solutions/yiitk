<?php

    namespace yiitk\console\controllers;

    use Yii;
    use yiitk\db\Migration;

    /**
     * Manages application migrations.
     */
    class MigrateController extends \yii\console\controllers\MigrateController
    {
        /**
         * @var bool
         */
        public bool $forceNamespace = false;

        /**
         * @var string
         */
        public string $forcedMigrationNamespace = 'console\migrations';

        /**
         * @var string
         */
        public string $baseMigrationClass = Migration::class;

        #region Initialization
        /**
         * {@inheritdoc}
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            $this->generatorTemplateFiles = [];

            Yii::setAlias('@yiitk/migrations/controllers', dirname(__DIR__));

            $this->templateFile = '@yiitk/migrations/controllers/views/migration.php';

            parent::init();
        }
        #endregion

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        protected function generateMigrationSourceCode($params)
        {
            if (!is_array($params)) {
                $params = [];
            }

            $extendedParams = [];
            $migrationName  = $params['name'];

            $extendedParams['migrationSubview'] = '_create';

            if ((bool)$this->forceNamespace && empty($params['namespace'])) {
                $params['namespace'] = $this->forcedMigrationNamespace;
            }

            if (preg_match('/^update_.*$/', $migrationName)) {
                $extendedParams['migrationSubview'] = '_update';
            } elseif (preg_match('/^drop_.*$/', $migrationName)) {
                $extendedParams['migrationSubview'] = '_drop';
            }

            $extendedParams['tableName']          = preg_replace('/^(create|update|drop)_(.*)$/', '$2', $migrationName);
            $extendedParams['migrationClass']     = $this->baseMigrationClass;
            $extendedParams['migrationClassName'] = preg_replace('/^(.*)\\\([A-Za-z0-9]+)$/', '$2', $extendedParams['migrationClass']);

            return $this->renderFile(Yii::getAlias($this->templateFile), array_merge($params, $extendedParams));
        }
    }
