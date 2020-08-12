<?php

    /**
     * @var string $tableName
     * @var string $className
     * @var string $namespace
     */

?>

        /**
         * @inheritdoc
         */
        public function safeUp()
        {
            if (!$this->columnExists($this->findCurrentTableName(), 'fieldName')) {
                $this->addColumn($this->findCurrentTableName(), 'fieldName', $this->string(255)->null()->after('id'));
            }
        }

        /**
         * @inheritdoc
         */
        public function safeDown()
        {
            if ($this->columnExists($this->findCurrentTableName(), 'fieldName')) {
                $this->dropColumn($this->findCurrentTableName(), 'fieldName');
            }
        }
