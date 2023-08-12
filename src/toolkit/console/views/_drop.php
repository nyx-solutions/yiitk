<?php

    /**
     * @var string $tableName
     * @var string $className
     * @var string $namespace
     */

?>

        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingParentCallCommonInspection
         */
        public function up()
        {
            if ($this->tableExists($this->findCurrentTableName())) {
                $this->dropTable($this->findCurrentTableName());
            }
        }

        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingParentCallCommonInspection
         */
        public function down()
        {
            echo "<?= $className; ?> cannot be reverted.\n";

            return false;
        }
