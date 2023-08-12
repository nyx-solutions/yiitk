<?php

    namespace yiitk\db\pgsql;

    use common\helpers\ConfigHelper;
    use Throwable;
    use yii\db\ColumnSchemaBuilder;
    use yii\db\Exception as DbException;
    use yii\db\Schema;
    use yiitk\db\SchemaBuilderTrait as BaseSchemaBuilderTrait;
    use yiitk\enum\base\BaseEnum;

    /**
     * PostgreSQL Schema Builder Trait
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
            return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TEXT);
        }

        /**
         * @inheritdoc
         */
        public function mediumText(array $options = []): ColumnSchemaBuilder
        {
            return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TEXT);
        }

        /**
         * @inheritdoc
         */
        public function longText(array $options = []): ColumnSchemaBuilder
        {
            return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TEXT);
        }
        #endregion

        #region ENUM
        /**
         * @inheritdoc
         */
        public function enum(array $options = []): ColumnSchemaBuilder
        {
            if (count($options) <= 0) {
                throw new DbException('ENUM column type needs at least one option.');
            }

            /** @var BaseEnum $baseClass */
            $baseClass = $options['class'] ?? null;

            if (empty($baseClass)) {
                throw new DbException('ENUM column type needs the class property.');
            }

            try {
                $type = $baseClass::uid();
            } catch (Throwable) {
                throw new DbException('ENUM must be an valid ENUM.');
            }

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
         */
        public function addHashableColumn(string $table, string $column = 'hash', bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', ?int $length = 45, int $maxUpdatePerCicle = 500): bool
        {
            return true;
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
         */
        public function addExternalIdColumn(string $table, string $column = 'externalId', string $uniqueIndexName = 'external_id', array $uniqueIndexAdditionalColumns = [], bool $dropIfExists = true, string $afterAttribute = 'id', string $idAttribute = 'id', int $length = 45, int $maxUpdatePerCicle = 500): bool
        {
            return true;
        }
        #endregion
        #endregion
        #endregion
    }
