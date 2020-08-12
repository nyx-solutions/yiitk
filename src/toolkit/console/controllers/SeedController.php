<?php

    namespace yiitk\console\controllers;

    use DirectoryIterator;
    use Yii;
    use yii\db\Connection;
    use yii\db\Exception;
    use yii\di\Instance;
    use yii\helpers\Console;
    use yiitk\console\Controller;
    use yiitk\db\ActiveRecord;

    /**
     * Manages application database seeds.
     *
     * @noinspection ContractViolationInspection
     */
    class SeedController extends Controller
    {
        /**
         * @var bool indicates whether the console output should be compacted.
         * If this is set to true, the individual commands ran within the migration will not be output to the console.
         * Default is false, in other words the output is fully verbose by default.
         */
        public bool $compact = false;

        /**
         * @var Connection|array|string the DB connection object or the application component ID of the DB connection
         * that this migration should work with. Starting from version 2.0.2, this can also be a configuration array
         * for creating the object.
         *
         * Note that when a Migration object is created by the `migrate` command, this property will be overwritten
         * by the command. If you do not want to use the DB connection provided by the command, you may override
         * the [[init()]] method like the following:
         *
         * ```php
         * public function init()
         * {
         *     $this->db = 'db2';
         *     parent::init();
         * }
         * ```
         */
        public $db = 'db';

        #region Initialization
        /**
         * Initializes the migration.
         * This method will set [[db]] to be the 'db' application component, if it is `null`.
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            $this->db = Instance::ensure($this->db, Connection::class);

            $this->db->getSchema()->refresh();

            $this->db->enableSlaves = false;
        }
        #endregion

        #region Actions
        /**
         * Seeds the database with stored data.
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function actionRun()
        {
            $version = Yii::getVersion();

            $this->stdout("Yii Seed Tool (based on Yii v{$version})\n\n");

            $runAll = Console::confirm('Do you want to run all seed actions? This will try to truncate populated tables.', false);

            $this->executeSeeds($runAll);
        }
        #endregion

        #region Seeds
        /**
         * @return array
         */
        protected function seeds(): array
        {
            return [];
        }
        #endregion

        #region Run
        /**
         * @param bool $all
         *
         * @throws Exception
         */
        protected function executeSeeds(bool $all = false): void
        {
            $seeds = $this->seeds();

            if (empty($seeds)) {
                $this->stdout("There are no seeds to populate tables.\n\n", Console::FG_RED);

                return;
            }

            $i = 1;

            foreach ($seeds as $seed) {
                if (isset($seed[0], $seed['model']) && is_string($seed[0]) && is_subclass_of($seed['model'], ActiveRecord::class)) {
                    $skip = ((isset($seed['skip'])) ? (bool)$seed['skip'] : false);

                    if ($skip) {
                        $this->stdout("Seed {$i}: Skipping execution by user request at seed configuration...\n\n", Console::FG_YELLOW);

                        $i++;

                        continue;
                    }

                    $name           = $seed[0];
                    $source         = ((!empty($seed['source'])) ? $seed['source'] : null);
                    $loadData       = ((isset($seed['loadData']) && is_callable($seed['loadData'])) ? $seed['loadData'] : null);
                    $model          = $seed['model'];
                    $tableName      = $model::tableName();
                    $seedMethodName = 'seed'.ucfirst($name);

                    if (!is_null($source)) {
                        if ($this->truncate($name, $model, !$all)) {
                            foreach ($this->loadFromSource($source) as $sourceItem) {
                                $data = $sourceItem;

                                if (!is_null($loadData)) {
                                    $data = $loadData($sourceItem);
                                }

                                if (is_array($data)) {
                                    $this->insert($tableName, $data);

                                }
                            }
                        } else {
                            $this->stdout("Seed {$i}: could not truncate the table {$tableName}. Skipping execution...\n\n", Console::FG_RED);
                        }
                    } elseif (method_exists($this, $seedMethodName)) {
                        if ($this->truncate($name, $model, !$all)) {
                            $this->$seedMethodName($tableName);
                        } else {
                            $this->stdout("Seed {$i}: user canceled or could not truncate the table {$tableName}. Skipping execution...\n\n", Console::FG_RED);
                        }
                    } else {
                        $this->stdout("Seed {$i}: proccess method not found. Skipping execution...\n\n", Console::FG_RED);
                    }
                } else {
                    $modelClass = ActiveRecord::class;

                    $this->stdout("Seed {$i}: the seed name is required at the 0 index and model should be present with an instance of {$modelClass}. Skipping execution...\n\n", Console::FG_RED);
                }

                $i++;
            }
        }

        /**
         * @param string $name
         * @param string $model
         * @param bool   $confirm
         *
         * @return bool
         */
        public function truncate(string $name, string $model, bool $confirm = true): bool
        {
            /** @noinspection PhpUndefinedMethodInspection */
            $tableName = $model::tableName();

            if ($confirm) {
                $run = Console::confirm("Do you want to run the seed {$name} ({$tableName})? This will try to truncate the {$tableName}.", false);

                if (!$run) {
                    return false;
                }
            }

            $truncate = true;

            try {
                $this->truncateTable($model);
            } catch (\Exception $exception) {
                $truncate = false;
            }

            if (!$truncate) {
                /** @noinspection PhpUndefinedMethodInspection */
                $primaryKey = $model::primaryKey();

                if (is_array($primaryKey) && !empty($primaryKey[0])) {
                    $primaryKey = $primaryKey[0];
                } else {
                    $primaryKey = null;
                }

                if (!is_null($primaryKey)) {
                    try {
                        $this->delete($tableName, "{$primaryKey} > 0");

                        /** @noinspection SqlNoDataSourceInspection */
                        $this->db->createCommand("ALTER TABLE {$tableName} AUTO_INCREMENT = 1")->execute();
                    } catch (\Exception $exception) {
                        return false;
                    }
                }
            }

            return true;
        }

        /**
         * @param string $path
         *
         * @return array
         *
         * @throws \Exception
         */
        protected function loadFromSource(string $path): array
        {
            $items = [];

            if (is_dir($path)) {
                foreach (new DirectoryIterator($path) as $file) {
                    if ($file->isFile() && $file->getExtension() === 'json') {
                        $contents = file_get_contents($path.'/'.$file->getFilename(), LOCK_EX);
                        $contents = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

                        if ($contents && is_array($contents)) {
                            foreach ($contents as $item) {
                                $items[] = $item;
                            }
                        }
                    }
                }
            }

            return $items;
        }

        /**
         * @param $action
         */
        protected function seed($action): void
        {
        }
        #endregion

        #region Command Methods
        /**
         * Prepares for a command to be executed, and outputs to the console.
         *
         * @param string $description the description for the command, to be output to the console.
         * @return float the time before the command is executed, for the time elapsed to be calculated.
         */
        protected function beginCommand($description): float
        {
            if (!$this->compact) {
                echo "    > $description ...";
            }
            return microtime(true);
        }

        /**
         * Finalizes after the command has been executed, and outputs to the console the time elapsed.
         *
         * @param float $time the time before the command was executed.
         */
        protected function endCommand($time): void
        {
            if (!$this->compact) {
                echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
            }
        }
        #endregion

        #region Data Base Methods
        /**
         * Creates and executes an INSERT SQL statement.
         * The method will properly escape the column names, and bind the values to be inserted.
         *
         * @param string $table   the table that new rows will be inserted into.
         * @param array  $columns the column data (name => value) to be inserted into the table.
         *
         * @throws Exception
         */
        public function insert($table, $columns): void
        {
            /** @noinspection SqlNoDataSourceInspection */
            $time = $this->beginCommand("insert into $table");

            $this->db->createCommand()->insert($table, $columns)->execute();

            $this->endCommand($time);
        }

        /**
         * Creates and executes a batch INSERT SQL statement.
         * The method will properly escape the column names, and bind the values to be inserted.
         *
         * @param string $table   the table that new rows will be inserted into.
         * @param array  $columns the column names.
         * @param array  $rows    the rows to be batch inserted into the table
         *
         * @throws Exception
         */
        public function batchInsert($table, $columns, $rows): void
        {
            /** @noinspection SqlNoDataSourceInspection */
            $time = $this->beginCommand("insert into $table");

            $this->db->createCommand()->batchInsert($table, $columns, $rows)->execute();

            $this->endCommand($time);
        }

        /**
         * Creates and executes an UPDATE SQL statement.
         * The method will properly escape the column names and bind the values to be updated.
         *
         * @param string       $table     the table to be updated.
         * @param array        $columns   the column data (name => value) to be updated.
         * @param array|string $condition the conditions that will be put in the WHERE part. Please
         *                                refer to [[Query::where()]] on how to specify conditions.
         * @param array        $params    the parameters to be bound to the query.
         *
         * @throws Exception
         */
        public function update($table, $columns, $condition = '', $params = []): void
        {
            $time = $this->beginCommand("update $table");

            $this->db->createCommand()->update($table, $columns, $condition, $params)->execute();

            $this->endCommand($time);
        }

        /**
         * Creates and executes a DELETE SQL statement.
         *
         * @param string       $table     the table where the data will be deleted from.
         * @param array|string $condition the conditions that will be put in the WHERE part. Please
         *                                refer to [[Query::where()]] on how to specify conditions.
         * @param array        $params    the parameters to be bound to the query.
         *
         * @throws Exception
         */
        public function delete($table, $condition = '', $params = []): void
        {
            /**
             * @noinspection SqlNoDataSourceInspection
             * @noinspection SqlWithoutWhere
             */
            $time = $this->beginCommand("delete from $table");

            $this->db->createCommand()->delete($table, $condition, $params)->execute();

            $this->endCommand($time);
        }

        /**
         * Builds and executes a SQL statement for truncating a DB table.
         *
         * @param string $table the table to be truncated. The name will be properly quoted by the method.
         *
         * @throws Exception
         */
        public function truncateTable($table): void
        {
            $time = $this->beginCommand("truncate table $table");

            $this->db->createCommand()->truncateTable($table)->execute();

            $this->endCommand($time);
        }
        #endregion

        #region Getters
        /**
         * @return array|string|Connection
         */
        protected function getDb()
        {
            return $this->db;
        }
        #endregion
    }
