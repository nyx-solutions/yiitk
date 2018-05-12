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
            if ($this->tableExists($this->getCurrentTableName())) {
                $this->dropTable($this->getCurrentTableName());
            }
        }

        /**
         * {@inheritdoc}
         */
        public function safeDown()
        {
            echo "<?= $className; ?> cannot be reverted.\n";

            return false;
        }
