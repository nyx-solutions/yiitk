<?php

    /**
     * @var string $tableName
     * @var string $tableSimpleName
     * @var string $className
     * @var string $namespace
     */

?>

        /**
         * @inheritdoc
         */
        public function safeUp()
        {
            if ($this->tableExists($this->findCurrentTableName())) {
                $this->dropTable($this->findCurrentTableName());
            }
        }

        /**
         * @inheritdoc
         */
        public function safeDown()
        {
            echo "<?= $className; ?> cannot be reverted.\n";

            return false;
        }
