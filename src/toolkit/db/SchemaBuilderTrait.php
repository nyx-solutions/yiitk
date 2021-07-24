<?php

    namespace yiitk\db;

    use Exception;
    use Throwable;
    use yii\base\Exception as YiiException;
    use yii\base\NotSupportedException;
    use yii\db\Connection;
    use yii\db\Exception as DbException;
    use yii\db\ColumnSchemaBuilder;
    use yii\db\Query;
    use yii\db\Schema;
    use yiitk\helpers\HashableHelper;
    use function count;
    use function is_array;

    /**
     * Trait SchemaBuilderTrait
     */
    trait SchemaBuilderTrait
    {
        use \yii\db\SchemaBuilderTrait;

        /**
         * @var bool
         */
        protected bool $useJsonColumn = true;

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
         * Creates a ENUM column
         *
         * @param array $options
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws DbException
         * @throws NotSupportedException
         */
        public function enum(array $options = []): ColumnSchemaBuilder
        {
            if (!$this->isUsingMySqlDriver()) {
                throw new DbException('ENUM column type is only supported in MySQL.');
            }

            if (count($options) <= 0) {
                throw new DbException('ENUM column type needs at least one option.');
            }

            $type = '';

            foreach ($options as $option) {
                $type .= ((!empty($type)) ? ', ' : '')."'{$option}'";
            }

            $type = "enum($type)";

            return $this->getDb()->getSchema()->createColumnSchemaBuilder($type);
        }

        /**
         * Creates a medium text column.
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws NotSupportedException
         */
        public function mediumText(): ColumnSchemaBuilder
        {
            if (!$this->isUsingMySqlDriver()) {
                return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TEXT);
            }

            return $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext');
        }

        /**
         * Creates a long text column.
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws NotSupportedException
         */
        public function longText(): ColumnSchemaBuilder
        {
            if (!$this->isUsingMySqlDriver()) {
                return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TEXT);
            }

            return $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext');
        }

        /**
         * Creates a tiny text column.
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws NotSupportedException
         */
        public function tinyText(): ColumnSchemaBuilder
        {
            if (!$this->isUsingMySqlDriver()) {
                return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TEXT);
            }

            return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinytext');
        }

        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function json()
        {
            if ($this->useJsonColumn) {
                return parent::json();
            }

            return $this->longText();
        }

        /**
         * @param int|null $length
         * @param bool     $null
         * @param bool     $unique
         *
         * @return ColumnSchemaBuilder
         *
         * @noinspection ParameterDefaultValueIsNotNullInspection
         */
        public function hash(?int $length = 45, bool $null = false, bool $unique = true): ColumnSchemaBuilder
        {
            $field = $this->string($length);

            if ($null) {
                $field->null();
            } else {
                $field->notNull();
            }

            if ($unique) {
                $field->unique();
            }

            return $field;
        }

        /**
         * @param int  $length
         * @param bool $null
         * @param bool $unique
         *
         * @return ColumnSchemaBuilder
         */
        public function externalId(int $length = 45, bool $null = false, bool $unique = true): ColumnSchemaBuilder
        {
            $field = $this->string($length);

            if ($null) {
                $field->null();
            } else {
                $field->notNull();
            }

            if ($unique) {
                $field->unique();
            }

            return $field;
        }

        /**
         * @param string   $table
         * @param string   $column
         * @param bool     $dropIfExists
         * @param string   $afterAttribute
         * @param string   $idAttribute
         * @param int|null $length
         * @param int      $maxUpdatePerCicle
         *
         * @return bool
         *
         * @throws YiiException
         *
         * @noinspection ParameterDefaultValueIsNotNullInspection
         */
        public function addHashableColumn(string $table, string $column = 'hash', bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', ?int $length = 45, int $maxUpdatePerCicle = 500): bool
        {
            if ($this->tableExists($table)) {
                if (!$this->columnExists($table, $column)) {
                    $this->addColumn(
                        $table,
                        $column,
                        $this->hash($length, true, false)->after($afterAttribute)
                    );

                    $hasResults = true;

                    while ($hasResults) {
                        $records = (new Query())
                            ->select($idAttribute)
                            ->from($table)
                            ->where([$column => null])
                            ->limit($maxUpdatePerCicle)
                            ->all();

                        if (is_array($records) && !empty($records)) {
                            $records = array_map(
                                static fn ($record) => (int)$record[$idAttribute],
                                $records
                            );

                            foreach ($records as $id) {
                                if (!empty($id)) {
                                    $this->update(
                                        $table,
                                        [
                                            $column => HashableHelper::uniqueHash(
                                                fn ($hash) => (new Query())->select($idAttribute)->from($table)->where([$column => $hash])->exists()
                                            )
                                        ],
                                        [$idAttribute => $id]
                                    );
                                }
                            }
                        } else {
                            $hasResults = false;
                        }
                    }

                    $this->alterColumn(
                        $table,
                        $column,
                        $this->hash($length)
                    );

                    return true;
                }

                if ($dropIfExists) {
                    try {
                        $this->dropIndex($column, $table);
                    } catch (Exception) {}

                    try {
                        $this->dropColumn($table, $column);
                    } catch (Throwable) {}

                    return $this->addHashableColumn($table, $column, false, $afterAttribute, $idAttribute, $length, $maxUpdatePerCicle);
                }

                return false;
            }

            throw new YiiException('The method "addHashableColumn" can only be called when the table already exists and the target column does not exist in the table context. To new tables or to update columns, use the "hash" method.');
        }

        /**
         * @param string $table
         * @param string $column
         * @param string $uniqueIndexName
         * @param array  $uniqueIndexAdditionalColumns
         * @param bool   $dropIfExists
         * @param string $afterAttribute
         * @param string $idAttribute
         * @param int    $length
         * @param int    $maxUpdatePerCicle
         *
         * @return bool
         *
         * @throws YiiException
         */
        public function addExternalIdColumn(string $table, string $column = 'externalId', string $uniqueIndexName = 'external_id', array $uniqueIndexAdditionalColumns = [], bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', int $length = 45, int $maxUpdatePerCicle = 500): bool
        {
            if ($this->tableExists($table)) {
                if (!$this->columnExists($table, $column)) {
                    $this->addColumn(
                        $table,
                        $column,
                        $this->externalId($length, true, false)->after($afterAttribute)
                    );

                    $hasResults = true;

                    while ($hasResults) {
                        $records = (new Query())
                            ->select($idAttribute)
                            ->from($table)
                            ->where([$column => null])
                            ->limit($maxUpdatePerCicle)
                            ->all();

                        if (is_array($records) && !empty($records)) {
                            $records = array_map(
                                static fn ($record) => (int)$record[$idAttribute],
                                $records
                            );

                            foreach ($records as $id) {
                                if (!empty($id)) {
                                    $this->update(
                                        $table,
                                        [
                                            $column => HashableHelper::uniqueHash(
                                                fn ($hash) => (new Query())->select($idAttribute)->from($table)->where([$column => $hash])->exists(),
                                                false
                                            )
                                        ],
                                        [$idAttribute => $id]
                                    );
                                }
                            }
                        } else {
                            $hasResults = false;
                        }
                    }

                    $this->alterColumn(
                        $table,
                        $column,
                        $this->externalId($length, unique: false)
                    );

                    if (empty($uniqueIndexName)) {
                        $uniqueIndexName = 'external_id';
                    }

                    $uniqueIndexColumns = [$column];

                    if (!empty($uniqueIndexAdditionalColumns)) {
                        $uniqueIndexColumns = array_merge($uniqueIndexColumns, $uniqueIndexAdditionalColumns);
                    }

                    $this->createIndex($uniqueIndexName, $table, $uniqueIndexColumns, true);

                    return true;
                }

                if ($dropIfExists) {
                    try {
                        $this->dropIndex($uniqueIndexName, $table);
                    } catch (Exception) {}

                    try {
                        $this->dropColumn($table, $column);
                    } catch (Throwable) {}

                    return $this->addExternalIdColumn($table, $column, $uniqueIndexName, $uniqueIndexAdditionalColumns, false, $afterAttribute, $idAttribute, $length, $maxUpdatePerCicle);
                }

                return false;
            }

            throw new YiiException('The method "addExternalIdColumn" can only be called when the table already exists and the target column does not exist in the table context. To new tables or to update columns, use the "hash" method.');
        }
    }
