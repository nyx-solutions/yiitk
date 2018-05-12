<?php

    /**
     * @var string $tableName
     * @var string $tableSimpleName
     * @var string $className
     * @var string $namespace
     */

?>

        /**
         * {@inheritdoc}
         */
        public function safeUp()
        {
            if (!$this->columnExists($this->getCurrentTableName(), 'fieldName')) {
                $this->addColumn($this->getCurrentTableName(), 'fieldName', $this->string(255)->null()->after('id'));
            }
        }

        /**
         * {@inheritdoc}
         */
        public function safeDown()
        {
            if ($this->columnExists($this->getCurrentTableName(), 'fieldName')) {
                $this->dropColumn($this->getCurrentTableName(), 'fieldName');
            }
        }
