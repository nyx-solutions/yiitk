<?php

    namespace yiitk\db;

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
        protected $useJsonColumn = true;

        /**
         * @return \yii\db\Connection the database connection to be used for schema building.
         */
        protected abstract function getDb();

        /**
         * @return bool
         */
        protected abstract function isUsingMySqlDriver();

        /**
         * Creates a ENUM column
         *
         * @param array $options
         *
         * @return ColumnSchemaBuilder the column instance which can be further customized.
         *
         * @throws \yii\base\Exception
         * @throws \yii\base\NotSupportedException
         */
        public function enum($options = [])
        {
            if (!$this->isUsingMySqlDriver()) {
                throw new \yii\base\Exception('ENUM column type is only supported in MySQL.');
            }

            if (!is_array($options) || count($options) <= 0) {
                throw new \yii\base\Exception('ENUM column type needs at least one option.');
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
         * @throws \yii\base\NotSupportedException
         */
        public function mediumText()
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
         * @throws \yii\base\NotSupportedException
         */
        public function longText()
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
         * @throws \yii\base\NotSupportedException
         */
        public function tinyText()
        {
            if (!$this->isUsingMySqlDriver()) {
                return $this->getDb()->getSchema()->createColumnSchemaBuilder(Schema::TYPE_TEXT);
            }

            return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinytext');
        }

        /**
         * @inheritdoc
         */
        public function json()
        {
            if ($this->useJsonColumn) {
                return parent::json();
            }

            return $this->longText();
        }
    }
