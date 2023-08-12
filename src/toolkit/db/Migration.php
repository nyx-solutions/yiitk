<?php

    namespace yiitk\db;

    use yiitk\db\migrations\MigrationSeedTrait;
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
        use MigrationSeedTrait;

        protected const HASH_ALGORITHM  = 'crc32';
        protected const NAME_MAX_LENGTH = 31;

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

        #region Actions
        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function up()
        {
            $up = parent::up();

            if ($up !== false) {
                $this->applySeeds();

                return $up;
            }

            return $up;
        }

        /**
         * @inheritdoc
         */
        public function down()
        {
            parent::down();

            $this->dropTable($this->findCurrentTableName());
        }
        #endregion

        #region Tables
        /**
         * @param string $table
         *
         * @return bool
         *
         * @throws NotSupportedException
         */
        public function tableExists(string $table): bool
        {
            $schema        = $this->db->getSchema();
            $schemaName    = $schema->defaultSchema;
            $tables        = $schema->getTableNames($schemaName, true);
            $realTableName = $schema->getRawTableName($table);

            return (in_array($realTableName, $tables));
        }
        #endregion

        #region Columns
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
                $tableSchema = $this->db->getSchema()->getTableSchema($table);

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
        #endregion

        #region Foreign Keys
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
         * @param string       $name
         * @param string       $table
         * @param string|array $columns
         * @param string       $refTable
         * @param string|array $refColumns
         * @param string|null  $delete
         * @param string|null  $update
         *
         * @return void
         */
        public function addUniqueForeignKey(
            string $name,
            string $table,
            string|array $columns,
            string $refTable,
            string|array $refColumns,
            ?string $delete = null,
            ?string $update = null
        ): void
        {
            $indexName = $this->generateIndexName($name);

            $this->createIndex($indexName, $table, $columns, true);

            parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
        }

        /**
         * @param string       $name
         * @param string       $table
         * @param string|array $columns
         * @param string       $refTable
         * @param string|array $refColumns
         * @param string|null  $delete
         * @param string|null  $update
         *
         * @return void
         *
         * @see addForeignKey
         */
        public function addForeignKeyWithoutIndex(
            string $name,
            string $table,
            string|array $columns,
            string $refTable,
            string|array $refColumns,
            ?string $delete = null,
            ?string $update = null
        ): void
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
        #endregion

        #region Indexes
        /**
         * @param string $name
         *
         * @return string
         */
        protected function generateIndexName(string $name): string
        {
            $indexName = trim($name);

            if (str_ends_with($indexName, '}}')) {
                $indexName = preg_replace('/^(.*)}}$/', '$1_idx}}', $indexName);
            } else {
                $indexName .= '_idx';
            }

            return $indexName;
        }
        #endregion

        #region Views
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
         * @return void
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
         * @return void
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
        #endregion

        #region Table & Field Names
        /**
         * @return string
         */
        protected function findSimpleTableName(): string
        {
            return preg_replace(['/^{{%/', '/}}$/'], '', (string)$this->tableName);
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
        public function findFieldName(string $name, int $max = -1, bool $exception = false): string
        {
            $max               = $this->normalizeDefaultMaxChars($max);
            $tableName         = InflectorHelper::camel2id(((!empty($this->db->tablePrefix)) ? "{$this->db->tablePrefix}_" : '') . $this->findSimpleTableName(), '_');
            $tableNameInitials = implode('', explode('_', $tableName));
            $fieldName         = InflectorHelper::camel2id($name, '_');

            if ($exception && strlen($fieldName) > $max) {
                throw new DbException("The field name {$fieldName} is bigger than the max size ({$max}).");
            }

            return "{$tableNameInitials}_{$fieldName}";
        }

        /**
         * @param string $name
         * @param bool   $useHash
         * @param bool   $useTablePrefix
         *
         * @return string
         */
        public function withPrefix(string $name, bool $useHash = false, bool $useTablePrefix = true): string
        {
            $table = $this->findSimpleTableName();
            $name  = (string)InflectorHelper::camel2id($name, '_');

            if ($useHash) {
                $table = (string)hash(self::HASH_ALGORITHM, $table);
            }

            if ($useTablePrefix) {
                return '{{%'.$table.'_'.$name.'}}';
            }

            return "{$table}_{$name}";
        }

        /**
         * @param string $name
         * @param bool   $useHash
         * @param bool   $useTablePrefix
         *
         * @return string
         */
        public function withTableName(string $name, bool $useHash = true, bool $useTablePrefix = false): string
        {
            return $this->withPrefix($name, $useHash, $useTablePrefix);
        }
        #endregion

        #region Helpers
        /**
         * @return string
         */
        protected function getTableOptions(): string
        {
            return (string)$this->tableOptions;
        }

        /**
         * @param int $max
         *
         * @return int
         */
        protected function normalizeDefaultMaxChars(int $max = -1): int
        {
            if ($max === -1) {
                $max = self::NAME_MAX_LENGTH;
            }

            return $max;
        }

        #region Drivers
        /**
         * @param string $name
         *
         * @return bool
         */
        protected function isUsingDriver(string $name): bool
        {
            return (strtolower($this->getDb()->getDriverName()) === $name);
        }

        /**
         * @return bool
         */
        protected function isUsingMySqlDriver(): bool
        {
            return $this->isUsingDriver('mysql');
        }

        /**
         * @return bool
         */
        protected function isUsingPostgreSqlDriver(): bool
        {
            return $this->isUsingDriver('pgsql');
        }
        #endregion
        #endregion
    }
