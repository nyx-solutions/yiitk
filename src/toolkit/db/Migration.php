<?php

    namespace yiitk\db;

    use yiitk\helpers\InflectorHelper;
    use yii\base\NotSupportedException;
    use yii\db\Connection;

    /**
     * Class Migration
     */
    class Migration extends \yii\db\Migration
    {
        use SchemaBuilderTrait;

        const ROW_FORMAT_COMPACT    = 'COMPACT';
        const ROW_FORMAT_REDUNDANT  = 'REDUNDANT';
        const ROW_FORMAT_DYNAMIC    = 'DYNAMIC';
        const ROW_FORMAT_COMPRESSED = 'COMPRESSED';

        const NAME_MAX_LENGTH       = 31;

        /**
         * @var bool
         */
        protected $onlyMySql = true;

        /**
         * @var integer
         */
        protected $pkLength = 20;

        /**
         * @var integer
         */
        protected $fkLength = 20;

        /**
         * @var string
         */
        protected $tableName;

        /**
         * @var string
         */
        protected $tableOptions;

        /**
         * @var string
         */
        public $tableCharset = 'utf8';

        /**
         * @var string
         */
        public $tableCollate = 'utf8_unicode_ci';

        /**
         * @var string
         */
        public $tableEngine = 'InnoDB';

        /**
         * @var bool
         */
        public $useMysqlInnoDbRowFormat = true;

        /**
         * @var bool
         */
        public $useMysqlInnoDbBarracudaFileFormat = false;

        /**
         * @var string
         */
        public $mysqlInnoDbRowFormat = self::ROW_FORMAT_DYNAMIC;

        #region Initialization
        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function init()
        {
            parent::init();

            if ($this->isUsingMySqlDriver()) {
                $rowFormat = '';

                if ($this->useMysqlInnoDbRowFormat && strtolower($this->tableEngine) == 'innodb') {
                    $rowFormat = " ROW_FORMAT={$this->mysqlInnoDbRowFormat}";
                }

                if ($this->useMysqlInnoDbRowFormat && $this->useMysqlInnoDbBarracudaFileFormat && strtolower($this->tableEngine) == 'innodb') {
                    $rowFormat = " ROW_FORMAT=".self::ROW_FORMAT_COMPRESSED;
                }

                $this->tableOptions = "CHARACTER SET {$this->tableCharset} COLLATE {$this->tableCollate}{$rowFormat} ENGINE={$this->tableEngine}";
            } else {
                if ((bool)$this->onlyMySql) {
                    throw new NotSupportedException('MySQL required.');
                }
            }
        }
        #endregion

        #region DataBase
        /**
         * @inheritdoc
         */
        public function safeDown()
        {
            $this->dropTable($this->findCurrentTableName());
        }

        #region DataBase Tables
        /**
         * @param string $table
         *
         * @return bool
         *
         * @throws NotSupportedException
         */
        public function tableExists($table)
        {
            $db     = \Yii::$app->db;
            $schema = $db->getSchema();

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
        public function columnExists($table, $column)
        {
            if ($this->tableExists($table)) {
                /** @var Connection $db */
                $db     = \Yii::$app->db;
                $schema = $db->getSchema();

                $columns = $schema->getTableSchema($table)->getColumnNames();

                return (in_array($column, $columns));
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
        public function fieldExists($table, $column)
        {
            return $this->columnExists($table, $column);
        }

        /**
         * @return string
         */
        protected function getTableOptions()
        {
            return $this->tableOptions;
        }
        #endregion

        #region DataBase FKs
        /**
         * @inheritdoc
         */
        public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
        {
            $indexName = $name;

            if (preg_match('/\}\}$/', $indexName)) {
                $indexName = preg_replace('/^(.*)\}\}$/', '$1_idx}}', $indexName);
            } else {
                $indexName .= '_idx';
            }

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
         * @param string       $delete     the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         * @param string       $update     the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         */
        public function addUniqueForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
        {
            $indexName = $name;

            if (preg_match('/\}\}$/', $indexName)) {
                $indexName = preg_replace('/^(.*)\}\}$/', '$1_idx}}', $indexName);
            } else {
                $indexName .= '_idx';
            }

            $this->createIndex($indexName, $table, $columns, true);

            parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
        }

        /**
         * @inheritdoc
         */
        public function dropForeignKey($name, $table)
        {
            $indexName = $name;

            if (preg_match('/\}\}$/', $indexName)) {
                $indexName = preg_replace('/^(.*)\}\}$/', '$1_idx}}', $indexName);
            } else {
                $indexName .= '_idx';
            }

            parent::dropForeignKey($name, $table);

            $this->dropIndex($indexName, $table);
        }

        /**
         * Builds a SQL statement for adding a foreign key constraint to an existing table (without index creation).
         * The method will properly quote the table and column names.
         * @param string $name the name of the foreign key constraint.
         * @param string $table the table that the foreign key constraint will be added to.
         * @param string|array $columns the name of the column to that the constraint will be added on. If there are multiple columns, separate them with commas or use an array.
         * @param string $refTable the table that the foreign key references to.
         * @param string|array $refColumns the name of the column that the foreign key references to. If there are multiple columns, separate them with commas or use an array.
         * @param string $delete the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         * @param string $update the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
         *
         * @see addForeignKey
         */
        public function addForeignKeyWithoutIndex($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
        {
            parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
        }
        #endregion

        #region DataBase Views
        /**
         * @param string $view
         *
         * @return bool
         */
        public function viewExists($view)
        {
            if (!$this->isUsingMySqlDriver()) {
                throw new \yii\base\Exception('The method viewExists is only supported in MySQL.');
            }

            return $this->tableExists($view);
        }

        /**
         * @param string $view
         * @param string $select
         */
        public function createView($view, $select)
        {
            if (!$this->isUsingMySqlDriver()) {
                throw new \yii\base\Exception('The method createView is only supported in MySQL.');
            }

            $this->execute("CREATE OR REPLACE VIEW {$view} AS {$select}");
        }

        /**
         * @param string $view
         */
        public function dropView($view)
        {
            if (!$this->isUsingMySqlDriver()) {
                throw new \yii\base\Exception('The method dropView is only supported in MySQL.');
            }

            if ($this->viewExists($view)) {
                $this->execute("DROP VIEW {$view}");
            }
        }
        #endregion

        #region DataBase Table Names
        /**
         * @return string
         */
        protected function findSimpleTableName()
        {
            $tableName = (string)$this->tableName;

            $tableName = preg_replace('/^\{\{%/', '', $tableName);
            $tableName = preg_replace('/\}\}$/', '', $tableName);

            return $tableName;
        }

        /**
         * @param string $name
         *
         * @return string
         */
        public function findTableName($name = '')
        {
            return '{{%'.((!empty($name)) ? $name : $this->findSimpleTableName()).'}}';
        }

        /**
         * @return string
         */
        public function findCurrentTableName()
        {
            return $this->findTableName();
        }

        /**
         * @return string
         */
        public function currentTableName()
        {
            return $this->findCurrentTableName();
        }

        /**
         * @param string  $name
         * @param integer $max
         *
         * @return string
         */
        public function findFieldName($name, $max = self::NAME_MAX_LENGTH)
        {
            $tableName            = InflectorHelper::camel2id(((!empty($this->db->tablePrefix)) ? "{$this->db->tablePrefix}_" : '').$this->findSimpleTableName(), '_');
            $tableNameInitials    = '';
            $tableNameInitialsAux = explode('_', $tableName);

            foreach ($tableNameInitialsAux as $initial) {
                $tableNameInitials .= $initial;
            }

            $fieldName = InflectorHelper::camel2id($name, '_');

            $newFieldName = "{$tableNameInitials}_{$fieldName}";

            return $newFieldName;
        }

        /**
         * @param string $name
         *
         * @return string
         */
        public function withTableName($name)
        {
            return '{{%'.$this->findSimpleTableName().'_'.InflectorHelper::camel2id($name, '_').'}}';
        }
        #endregion
        #endregion

        #region MySQL
        /**
         * @return bool
         */
        protected function isUsingMySqlDriver()
        {
            return (strtolower($this->getDb()->getDriverName()) == 'mysql');
        }
        #endregion
    }
