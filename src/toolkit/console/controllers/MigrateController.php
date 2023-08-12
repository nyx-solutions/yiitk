<?php

    namespace yiitk\console\controllers;

    use Yii;
    use yii\base\Exception as YiiException;
    use yii\console\controllers\MigrateController as YiiMigrateController;
    use yii\console\Exception as ConsoleException;
    use yii\console\ExitCode;
    use yii\helpers\Console;
    use yii\helpers\FileHelper;
    use yii\helpers\Inflector;
    use yiitk\db\Migration;

    /**
     * Manages application migrations.
     */
    class MigrateController extends YiiMigrateController
    {
        /**
         * @var bool
         */
        public bool $useYiiSourceCodeGeneration = false;

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

        /**
         * @var bool
         */
        public bool $useSubDirectories = true;

        /**
         * @var string
         */
        public string $baseMigrationsPath = '@console/migrations';

        /**
         * @var string
         */
        public string $queriesMigrationsPath = 'queries';

        /**
         * @var string
         */
        public string $tablesMigrationsPath = 'tables';

        /**
         * @var string
         */
        public string $viewsMigrationsPath = 'views';

        /**
         * @var string
         */
        public string $systemMigrationsPath = 'system';

        /**
         * @var string
         */
        public string $runnersMigrationsPath = 'runners';

        /**
         * @var string
         */
        public string $runnersRelativePath = '../';

        /**
         * @var string|null
         */
        protected ?string $migrationPrefix = null;

        #region Events
        /**
         * @inheritdoc
         */
        public function beforeAction($action)
        {
            $this->generatorTemplateFiles = [];

            Yii::setAlias('@yiitk/migrations/controllers', dirname(__DIR__));

            $this->templateFile = '@yiitk/migrations/controllers/views/migration.php';

            if (!$this->forceNamespace) {
                $this->forcedMigrationNamespace = '';
            }

            if ($this->useSubDirectories) {
                $this->migrationPath = "{$this->baseMigrationsPath}/{$this->runnersMigrationsPath}";
            }

            return parent::beforeAction($action);
        }
        #endregion

        #region Actions
        /**
         * Creates a new migration.
         *
         * This command creates a new migration using the available migration template.
         * After using this command, developers should modify the created migration
         * skeleton by filling up the actual migration logic.
         *
         * ```
         * yii migrate/create create_user_table
         * ```
         *
         * In order to generate a namespaced migration, you should specify a namespace before the migration's name.
         * Note that backslash (`\`) is usually considered a special character in the shell, so you need to escape it
         * properly to avoid shell errors or incorrect behavior.
         * For example:
         *
         * ```
         * yii migrate/create app\\migrations\\createUserTable
         * ```
         *
         * In case [[migrationPath]] is not set and no namespace is provided, the first entry of [[migrationNamespaces]] will be used.
         *
         * @param string $name the name of the new migration. This should only contain
         *                     letters, digits, underscores and/or backslashes.
         *
         * Note: If the migration name is of a special form, for example create_xxx or
         * drop_xxx, then the generated migration file will contain extra code,
         * in this case for creating/dropping tables.
         *
         * @return int
         * @throws ConsoleException if the name argument is invalid.
         * @throws YiiException
         */
        public function actionCreate($name)
        {
            if ($this->useYiiSourceCodeGeneration) {
                return parent::actionCreate($name);
            }

            $prefixPattern = '/^m(\d{6})_(\d{6})_(.*)$/';

            if (preg_match($prefixPattern, $name, $parts)) {
                [, $first, $second, $migrationName] = $parts;

                $this->migrationPrefix = "m{$first}_{$second}";

                $name = $migrationName;
            }

            if (!preg_match('/^[\w\\\\]+$/', $name)) {
                throw new ConsoleException('The migration name should contain letters, digits, underscore and/or backslash characters only.');
            }

            [$namespace, $className, $directory, $relativeName, $subdirectory] = $this->_generateClassName($name);

            // Abort if name is too long
            $nameLimit = $this->getMigrationNameLimit();

            if ($nameLimit !== null && strlen($className) > $nameLimit) {
                throw new ConsoleException('The migration name is too long.');
            }

            if ($this->useSubDirectories && empty($namespace)) {
                $migrationPath = Yii::getAlias($directory);
            } else {
                $migrationPath = $this->_findMigrationPath($namespace);
            }

            $fileBaseName = $className . '.php';
            $file = $migrationPath . DIRECTORY_SEPARATOR . $fileBaseName;

            if ($this->confirm("Create new migration '{$file}'?")) {
                $content = $this->generateMigrationSourceCode(['name' => $name, 'className' => $className, 'namespace' => $namespace]);

                FileHelper::createDirectory($migrationPath);

                if (file_put_contents($file, $content, LOCK_EX) === false) {
                    $this->stdout("Failed to create new migration.\n", Console::FG_RED);

                    return ExitCode::IOERR;
                }

                if ($this->useSubDirectories) {
                    $baseDir = Yii::getAlias(sprintf('%s%s%s', $this->baseMigrationsPath, DIRECTORY_SEPARATOR, $this->runnersMigrationsPath));

                    exec(sprintf('cd %s && ln -s %s%s/%s/%s ./%s', $baseDir, $this->runnersRelativePath, $subdirectory, $relativeName, $fileBaseName, $fileBaseName));
                }

                FileHelper::changeOwnership($file, $this->newFileOwnership, $this->newFileMode);

                $this->stdout("New migration created successfully.\n", Console::FG_GREEN);
            }

            return ExitCode::OK;
        }
        #endregion

        #region Source Code Generation
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

            if ($this->useYiiSourceCodeGeneration) {
                return parent::generateMigrationSourceCode($params);
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
            } elseif (preg_match('/^view_.*$/', $migrationName)) {
                $extendedParams['migrationSubview'] = '_view';
            } elseif (preg_match('/^system_.*$/', $migrationName)) {
                $extendedParams['migrationSubview'] = '_system';
            } elseif (preg_match('/^queries_.*$/', $migrationName)) {
                $extendedParams['migrationSubview'] = '_queries';
            }

            $extendedParams['tableName']          = preg_replace('/^(create|update|drop|queries|view|system)_(.*)$/', '$2', $migrationName);
            $extendedParams['migrationClass']     = $this->baseMigrationClass;
            $extendedParams['migrationClassName'] = preg_replace('/^(.*)\\\([A-Za-z0-9]+)$/', '$2', $extendedParams['migrationClass']);

            return $this->renderFile(Yii::getAlias($this->templateFile), array_merge($params, $extendedParams));
        }
        #endregion

        #region Helpers
        /**
         * Generates class base name and namespace from migration name from user input.
         * @param string $name migration name from user input.
         * @return array list of 2 elements: 'namespace' and 'class base name'
         * @since 2.0.10
         */
        private function _generateClassName($name)
        {
            $directory = null;
            $namespace = null;

            $name = trim($name, '\\');

            if (str_contains($name, '\\')) {
                $namespace = substr($name, 0, strrpos($name, '\\'));
                $name      = substr($name, strrpos($name, '\\') + 1);
            } elseif ($this->migrationPath === null) {
                $migrationNamespaces = $this->migrationNamespaces;
                $namespace           = array_shift($migrationNamespaces);
            }

            $migrationName = $name;

            if (!preg_match('/^(create|update|drop|queries|view|system)(.*)$/', $migrationName)) {
                $migrationName = "create_{$name}";
            }

            if ($this->migrationPrefix !== null) {
                $class = "{$this->migrationPrefix}_{$migrationName}";
            } elseif ($namespace === null) {
                $class = 'm' . gmdate('ymd_His') . '_' . $migrationName;
            } else {
                $class = 'M' . gmdate('ymdHis') . Inflector::camelize($migrationName);
            }

            $subdirectory = $this->tablesMigrationsPath;

            if (preg_match('/^(create|update|drop)_.*$/', $name)) {
                $subdirectory = $this->tablesMigrationsPath;
            } elseif (preg_match('/^view_.*$/', $name)) {
                $subdirectory = $this->viewsMigrationsPath;
            } elseif (preg_match('/^system_.*$/', $name)) {
                $subdirectory = $this->systemMigrationsPath;
            } elseif (preg_match('/^queries_.*$/', $name)) {
                $subdirectory = $this->queriesMigrationsPath;
            }

            $relativeName = null;

            if ($this->useSubDirectories) {
                $relativeName = str_replace('_', '-', preg_replace('/^(create|update|drop|queries|view|system)_(.*)$/', '$2', $name));
                $directory    = sprintf('%s%s%s%s%s', $this->baseMigrationsPath, DIRECTORY_SEPARATOR, $subdirectory, DIRECTORY_SEPARATOR, $relativeName);
            }

            return [$namespace, $class, $directory, $relativeName, $subdirectory];
        }

        /**
         * Finds the file path for the specified migration namespace.
         *
         * @param string|null $namespace migration namespace.
         *
         * @return string migration file path.
         * @throws ConsoleException on failure.
         * @since 2.0.10
         */
        private function _findMigrationPath($namespace)
        {
            if (empty($namespace)) {
                return ((is_array($this->migrationPath)) ? reset($this->migrationPath) : $this->migrationPath);
            }

            if (!in_array($namespace, $this->migrationNamespaces, true)) {
                throw new ConsoleException("Namespace '{$namespace}' not found in `migrationNamespaces`");
            }

            return $this->_getNamespacePath($namespace);
        }

        /**
         * Returns the file path matching the give namespace.
         * @param string $namespace namespace.
         * @return string file path.
         * @since 2.0.10
         */
        private function _getNamespacePath($namespace)
        {
            return str_replace('/', DIRECTORY_SEPARATOR, Yii::getAlias('@' . str_replace('\\', '/', $namespace)));
        }
        #endregion
    }

