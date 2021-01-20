<?php

    namespace yiitk\db;

    use Yii;
    use yii\base\Exception as YiiException;
    use yii\base\NotSupportedException;
    use yii\db\Exception as DbException;
    use yiitk\helpers\InflectorHelper;

    /**
     * Class Migration
     */
    class Migration extends \yii\db\Migration
    {
        use SchemaBuilderTrait;

        protected const ROW_FORMAT_COMPACT    = 'COMPACT';
        protected const ROW_FORMAT_REDUNDANT  = 'REDUNDANT';
        protected const ROW_FORMAT_DYNAMIC    = 'DYNAMIC';
        protected const ROW_FORMAT_COMPRESSED = 'COMPRESSED';

        protected const NAME_MAX_LENGTH       = 31;

        /**
         * @var bool
         */
        protected bool $onlyMySql = true;

        /**
         * @var int
         */
        protected int $pkLength = 20;

        /**
         * @var int
         */
        protected int $fkLength = 20;

        /**
         * @var string|null
         */
        protected ?string $tableName = null;

        /**
         * @var string|null
         */
        protected ?string $tableOptions = null;

        /**
         * @var string
         */
        public string $tableCharset = 'utf8';

        /**
         * @var string
         */
        public string $tableCollate = 'utf8_unicode_ci';

        /**
         * @var string
         */
        public string $tableEngine = 'InnoDB';

        /**
         * @var bool
         */
        public bool $useMysqlInnoDbRowFormat = true;

        /**
         * @var bool
         */
        public bool $useMysqlInnoDbBarracudaFileFormat = false;

        /**
         * @var string
         */
        public string $mysqlInnoDbRowFormat = self::ROW_FORMAT_DYNAMIC;

        //region Initialization
        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            parent::init();

            if ($this->isUsingMySqlDriver()) {
                $rowFormat = '';

                if ($this->useMysqlInnoDbRowFormat && strtolower($this->tableEngine) === 'innodb') {
                    $rowFormat = " ROW_FORMAT={$this->mysqlInnoDbRowFormat}";
                }

                if ($this->useMysqlInnoDbRowFormat && $this->useMysqlInnoDbBarracudaFileFormat && strtolower($this->tableEngine) === 'innodb') {
                    $rowFormat = " ROW_FORMAT=".self::ROW_FORMAT_COMPRESSED;
                }

                $this->tableOptions = "CHARACTER SET {$this->tableCharset} COLLATE {$this->tableCollate}{$rowFormat} ENGINE={$this->tableEngine}";
            } elseif ((bool)$this->onlyMySql) {
                throw new NotSupportedException('MySQL required.');
            }
        }
        //endregion

        //region DataBase
        /**
         * @inheritdoc
         */
        public function safeDown()
        {
            $this->dropTable($this->findCurrentTableName());
        }

        //region DataBase Tables
        /**
         * @param string $table
         *
         * @return bool
         *
         * @throws NotSupportedException
         */
        public function tableExists(string $table): bool
        {
            $schema = Yii::$app->db->getSchema();

            $tables        = $schema->getTableNames();
            $realTableName = $schema->getRawTableName($table);

            return (in_array($realTableName, $tables));
        }

        /**
         * @param string $table
         * @param string $column
         *
         * @return bool
         *
         * @throws NotSupportedException
         */
        public function columnExists(string $table, string $column): bool
        {
            if ($this->tableExists($table)) {
                $tableSchema = Yii::$app->db->getSchema()->getTableSchema($table);

                if ($tableSchema !== null) {
                    $columns = $tableSchema->getColumnNames();

                    return (in_array($column, $columns, true));
                }
            }

            return false;
        }

        /**
         * @param string $table
         * @param string $column
         *
         * @return bool
         *
         * @throws NotSupportedException
         */
        public function fieldExists(string $table, string $column): bool
        {
            return $this->columnExists($table, $column);
        }

        /**
         * @return string
         */
        protected function getTableOptions(): string
        {
            return (string)$this->tableOptions;
        }
        //endregion

        //region DataBase FKs
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
        {
            $indexName = $this->generateIndexName($name);

            $this->createIndex($indexName, $table, $columns);

            parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
        }

        /**
         * Builds a SQL statement for adding a foreign key constraint to an existing table.
         * The method will properly quote the table and column names.
         *
         * @param string       $name       the name of the foreign key constraint.
         * @param string       $table      the table that the foreign key constraint will be added to.
         * @param string|array $columns    the name of the column to that the constraint will be added on. If there are multiple columns, separate them with commas or use an array.
         * @param string       $refTable   the table that the foreign key references to.
         * @param string|array $refColumns the name of the column that the foreign key references to. If there are multiple columns, separate them with commas or use an array.
         * @param string|null  $delete     the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         * @param string|null  $update     the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         */
        public function addUniqueForeignKey(string $name, string $table, $columns, string $refTable, $refColumns, ?string $delete = null, ?string $update = null): void
        {
            $indexName = $this->generateIndexName($name);

            $this->createIndex($indexName, $table, $columns, true);

            parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
        }

        /**
         * Builds a SQL statement for adding a foreign key constraint to an existing table (without index creation).
         * The method will properly quote the table and column names.
         *
         * @param string       $name       the name of the foreign key constraint.
         * @param string       $table      the table that the foreign key constraint will be added to.
         * @param string|array $columns    the name of the column to that the constraint will be added on. If there are multiple columns, separate them with commas or use an array.
         * @param string       $refTable   the table that the foreign key references to.
         * @param string|array $refColumns the name of the column that the foreign key references to. If there are multiple columns, separate them with commas or use an array.
         * @param string|null  $delete     the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         * @param string|null  $update     the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         *
         * @see addForeignKey
         */
        public function addForeignKeyWithoutIndex(string $name, string $table, $columns, string $refTable, $refColumns, ?string $delete = null, ?string $update = null): void
        {
            parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
        }

        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function dropForeignKey($name, $table)
        {
            $indexName = $this->generateIndexName($name);

            parent::dropForeignKey($name, $table);

            $this->dropIndex($indexName, $table);
        }

        /**
         * @param string $name
         *
         * @return string
         */
        protected function generateIndexName(string $name): string
        {
            $indexName = $name;

            if (preg_match('/}}$/', $indexName)) {
                $indexName = preg_replace('/^(.*)}}$/', '$1_idx}}', $indexName);
            } else {
                $indexName .= '_idx';
            }

            return $indexName;
        }
        //endregion

        //region DataBase Views
        /**
         * @param string $view
         *
         * @return bool
         *
         * @throws NotSupportedException
         * @throws YiiException
         */
        public function viewExists(string $view): bool
        {
            if (!$this->isUsingMySqlDriver()) {
                throw new YiiException('The method viewExists is only supported in MySQL.');
            }

            return $this->tableExists($view);
        }

        /**
         * @param string $view
         * @param string $select
         *
         * @throws YiiException
         */
        public function createView(string $view, string $select): void
        {
            if (!$this->isUsingMySqlDriver()) {
                throw new YiiException('The method createView is only supported in MySQL.');
            }

            $this->execute("CREATE OR REPLACE VIEW {$view} AS {$select}");
        }

        /**
         * @param string $view
         *
         * @throws NotSupportedException
         * @throws YiiException
         */
        public function dropView(string $view): void
        {
            if (!$this->isUsingMySqlDriver()) {
                throw new YiiException('The method dropView is only supported in MySQL.');
            }

            if ($this->viewExists($view)) {
                $this->execute("DROP VIEW {$view}");
            }
        }
        //endregion

        //region DataBase Table Names
        /**
         * @return string
         */
        protected function findSimpleTableName(): string
        {
            $tableName = (string)$this->tableName;

            $tableName = preg_replace('/^{{%/', '', $tableName);
            $tableName = preg_replace('/}}$/', '', $tableName);

            return $tableName;
        }

        /**
         * @param string $name
         *
         * @return string
         */
        public function findTableName(string $name = ''): string
        {
            return '{{%'.((!empty($name)) ? $name : $this->findSimpleTableName()).'}}';
        }

        /**
         * @return string
         */
        public function findCurrentTableName(): string
        {
            return $this->findTableName();
        }

        /**
         * @return string
         */
        public function currentTableName(): string
        {
            return $this->findCurrentTableName();
        }

        /**
         * @param string $name
         * @param int    $max
         * @param bool   $exception
         *
         * @return string
         *
         * @throws DbException
         */
        public function findFieldName(string $name, int $max = self::NAME_MAX_LENGTH, bool $exception = false): string
        {
            $tableName            = InflectorHelper::camel2id(((!empty($this->db->tablePrefix)) ? "{$this->db->tablePrefix}_" : '').$this->findSimpleTableName(), '_');
            $tableNameInitials    = '';

            /** @noinspection MissUsingForeachInspection */
            foreach (explode('_', $tableName) as $initial) {
                $tableNameInitials .= $initial;
            }

            $fieldName = InflectorHelper::camel2id($name, '_');

            if ($exception && strlen($fieldName) > $max) {
                throw new DbException("The field name {$fieldName} is bigger than the max size ({$max})");
            }

            return "{$tableNameInitials}_{$fieldName}";
        }

        /**
         * @param string $name
         *
         * @return string
         */
        public function withTableName(string $name): string
        {
            return '{{%'.$this->findSimpleTableName().'_'.InflectorHelper::camel2id($name, '_').'}}';
        }
        //endregion
        //endregion

        //region MySQL
        /**
         * @return bool
         */
        protected function isUsingMySqlDriver(): bool
        {
            return (strtolower($this->getDb()->getDriverName()) === 'mysql');
        }
        //endregion
    }
