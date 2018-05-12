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
            if (!$this->tableExists($this->getCurrentTableName())) {
                $this->createTable(
                    $this->getCurrentTableName(),
                    [
                        'id'        => $this->bigPrimaryKey($this->pkLength),
                        'createdAt' => $this->dateTime()->notNull(),
                        'updatedAt' => $this->dateTime()->notNull()
                    ]
                );
            }
        }
