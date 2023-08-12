<?php

    namespace yiitk\db\mysql;

    use yiitk\db\SchemaBuilderTrait as BaseSchemaBuilderTrait;
    use common\helpers\ConfigHelper;
    use Throwable;
    use yii\base\Exception as YiiException;
    use yii\base\NotSupportedException;
    use yii\db\ColumnSchemaBuilder;
    use yii\db\Exception as DbException;
    use yii\db\Query;
    use yiitk\helpers\HashableHelper;

    /**
     * MySQL Schema Builder Trait
     */
    trait SchemaBuilderTrait
    {
        use BaseSchemaBuilderTrait;

        #region Columns
        #region Text
        /**
         * @inheritdoc
         */
        public function tinyText(array $options = []): ColumnSchemaBuilder
        {
            return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinytext');
        }

        /**
         * @inheritdoc
         */
        public function mediumText(array $options = []): ColumnSchemaBuilder
        {
            return $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext');
        }

        /**
         * @inheritdoc
         */
        public function longText(array $options = []): ColumnSchemaBuilder
        {
            return $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext');
        }
        #endregio
        #endregion

        #region ENUM
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
        #endregion

        #region Hash
        /**
         * @inheritdoc
         */
        public function hash(array $options = []): ColumnSchemaBuilder
        {
            [$length, $null, $unique] = ConfigHelper::extract(
                $options,
                [
                    'length' => ConfigHelper::integer(45),
                    'null'   => ConfigHelper::boolean(false),
                    'unique' => ConfigHelper::boolean(),
                ]
            );

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

        #region Default Data (Column Config)
        /**
         * @inheritdoc
         *
         * @throws YiiException
         */
        public function addHashableColumn(string $table, string $column = 'hash', bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', ?int $length = 45, int $maxUpdatePerCicle = 500): bool
        {
            if ($this->tableExists($table)) {
                if (!$this->columnExists($table, $column)) {
                    $this->addColumn(
                        $table,
                        $column,
                        $this->hash(['length' => $length, 'null' => true, 'unique' => false])->after($afterAttribute)
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
                        $this->hash(['length' => $length])
                    );

                    return true;
                }

                if ($dropIfExists) {
                    try {
                        $this->dropIndex($column, $table);
                    } catch (Throwable) {}

                    try {
                        $this->dropColumn($table, $column);
                    } catch (Throwable) {}

                    return $this->addHashableColumn($table, $column, false, $afterAttribute, $idAttribute, $length, $maxUpdatePerCicle);
                }

                return false;
            }

            throw new YiiException('The method "addHashableColumn" can only be called when the table already exists and the target column does not exist in the table context. To new tables or to update columns, use the "hash" method.');
        }
        #endregion
        #endregion

        #region External ID
        /**
         * @inheritdoc
         */
        public function externalId(array $options = []): ColumnSchemaBuilder
        {
            [$length, $null, $unique] = ConfigHelper::extract(
                $options,
                [
                    'length' => ConfigHelper::integer(45),
                    'null'   => ConfigHelper::boolean(false),
                    'unique' => ConfigHelper::boolean(),
                ]
            );

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

        #region Default Data (Column Config)
        /**
         * @inheritdoc
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
                        $this->externalId(['length' => $length, 'null' => true, 'unique' => false])->after($afterAttribute)
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
                        $this->externalId(['length' => $length, 'unique' => false])
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
                    } catch (Throwable) {}

                    try {
                        $this->dropColumn($table, $column);
                    } catch (Throwable) {}

                    return $this->addExternalIdColumn($table, $column, $uniqueIndexName, $uniqueIndexAdditionalColumns, false, $afterAttribute, $idAttribute, $length, $maxUpdatePerCicle);
                }

                return false;
            }

            throw new YiiException('The method "addExternalIdColumn" can only be called when the table already exists and the target column does not exist in the table context. To new tables or to update columns, use the "hash" method.');
        }
        #endregion
        #endregion
        #endregion
    }
