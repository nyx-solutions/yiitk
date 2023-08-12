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
            if (!$this->columnExists($this->findCurrentTableName(), 'fieldName')) {
                $this->addColumn($this->findCurrentTableName(), 'fieldName', $this->string(255)->null()->after('id'));
            }
        }

        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingParentCallCommonInspection
         */
        public function down()
        {
            if ($this->columnExists($this->findCurrentTableName(), 'fieldName')) {
                $this->dropColumn($this->findCurrentTableName(), 'fieldName');
            }
        }
