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
            if (!$this->tableExists($this->findCurrentTableName())) {
                $this->createTable(
                    $this->findCurrentTableName(),
                    [
                        'id'        => $this->bigPrimaryKey($this->pkLength),
                        'createdAt' => $this->dateTime()->notNull(),
                        'updatedAt' => $this->dateTime()->notNull()
                    ],
                    $this->tableOptions
                );
            }
        }
