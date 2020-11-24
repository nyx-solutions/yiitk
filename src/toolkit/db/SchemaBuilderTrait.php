<?php

    namespace yiitk\db;

    use yii\base\NotSupportedException;
    use yii\db\Connection;
    use yii\db\Exception as DbException;
    use yii\db\ColumnSchemaBuilder;
    use yii\db\Schema;

    /**
     * Trait FileManagerSchemaBuilderTrait
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
         * @noinspection ReturnTypeCanBeDeclaredInspection
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
         */
        public function json(): ColumnSchemaBuilder
        {
            if ($this->useJsonColumn) {
                return parent::json();
            }

            return $this->longText();
        }
    }
