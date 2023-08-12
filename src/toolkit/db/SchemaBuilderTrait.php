<?php

    namespace yiitk\db;

    use yii\base\NotSupportedException;
    use yii\db\ColumnSchemaBuilder;
    use yii\db\Connection;
    use yii\db\SchemaBuilderTrait as YiiSchemaBuilderTrait;

    /**
     * Schema Builder Trait
     */
    trait SchemaBuilderTrait
    {
        use YiiSchemaBuilderTrait {
            json as private _json;
        }

        /**
         * @var bool
         */
        protected bool $useJsonColumn = true;

        #region Abstract Inheritance
        /**
         * @return Connection the database connection to be used for schema building.
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        abstract protected function getDb();

        /**
         * @return bool
         */
        abstract protected function isUsingMySqlDriver(): bool;

        /**
         * @return bool
         */
        abstract protected function isUsingPostgreSqlDriver(): bool;
        #endregion

        #region Columns
        #region Text
        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function tinyText(array $options = []): ColumnSchemaBuilder
        {
            $this->throwNotSupportedException(__METHOD__);
        }

        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function mediumText(array $options = []): ColumnSchemaBuilder
        {
            $this->throwNotSupportedException(__METHOD__);
        }

        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function longText(array $options = []): ColumnSchemaBuilder
        {
            $this->throwNotSupportedException(__METHOD__);
        }
        #endregion

        #region ENUM
        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function enum(array $options = []): ColumnSchemaBuilder
        {
            $this->throwNotSupportedException(__METHOD__);
        }
        #endregion

        #region JSON
        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function json()
        {
            if ($this->useJsonColumn) {
                return $this->_json();
            }

            return $this->longText();
        }
        #endregion

        #region Hash
        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function hash(array $options = []): ColumnSchemaBuilder
        {
            $this->throwNotSupportedException(__METHOD__);
        }

        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function addHashableColumn(string $table, string $column = 'hash', bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', ?int $length = 45, int $maxUpdatePerCicle = 500): bool
        {
            $this->throwNotSupportedException(__METHOD__);
        }
        #endregion

        #region External ID
        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function externalId(array $options = []): ColumnSchemaBuilder
        {
            $this->throwNotSupportedException(__METHOD__);
        }

        /**
         * @inheritdoc
         *
         * @throws NotSupportedException
         */
        public function addExternalIdColumn(string $table, string $column = 'externalId', string $uniqueIndexName = 'external_id', array $uniqueIndexAdditionalColumns = [], bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', int $length = 45, int $maxUpdatePerCicle = 500): bool
        {
            $this->throwNotSupportedException(__METHOD__);
        }
        #endregion
        #endregion

        #region Helpers
        /**
         * @param string $method
         *
         * @return void
         *
         * @throws NotSupportedException
         */
        protected function throwNotSupportedException(string $method): void
        {
            throw new NotSupportedException(sprintf('The current DB driver %s does not support the %s column.', $this->getDb()->driverName, $method));
        }
        #endregion
    }
