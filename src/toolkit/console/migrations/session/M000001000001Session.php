<?php

    namespace yiitk\console\migrations\session;

    use yiitk\db\Migration;
    use yiitk\Module;

    /**
     * Migration: Sessão
     */
    class M000001000001Session extends Migration
    {
        /**
         * @var string
         */
        public string $tableEngine = 'MyISAM';

        /**
         * @var bool
         */
        public bool $useMysqlInnoDbRowFormat = false;

        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingParentCallCommonInspection
         */
        public function up()
        {
            /** @var Module $yiitk */
            $yiitk = Module::getInstance();

            $columns = [
                'id'           => $this->char(40)->notNull(),
                'expire'       => $this->integer(11)->null(),
                'data'         => $this->binary()->null()
            ];

            $sessionTable         = '{{%session}}';
            $sessionBackendTable  = '{{%session_backend}}';
            $sessionFrontendTable = '{{%session_frontend}}';
            $sessionApiTable      = '{{%session_api}}';

            if ($yiitk->sessionDb['db']) {
                $this->createTable($sessionTable, $columns, $this->getTableOptions());

                $this->tableName = $sessionTable;

                $this->addPrimaryKey($this->withTableName('id'), $sessionTable, 'id');
            }

            if ($yiitk->sessionDb['dbBackend']) {
                $this->createTable($sessionBackendTable, $columns, $this->getTableOptions());

                $this->tableName = $sessionBackendTable;

                $this->addPrimaryKey($this->withTableName('id'), $sessionBackendTable, 'id');
            }

            if ($yiitk->sessionDb['dbFrontend']) {
                $this->createTable($sessionFrontendTable, $columns, $this->getTableOptions());

                $this->tableName = $sessionFrontendTable;

                $this->addPrimaryKey($this->withTableName('id'), $sessionFrontendTable, 'id');
            }

            if ($yiitk->sessionDb['dbApi']) {
                $this->createTable($sessionApiTable, $columns, $this->getTableOptions());

                $this->tableName = $sessionApiTable;

                $this->addPrimaryKey($this->withTableName('id'), $sessionApiTable, 'id');
            }
        }
    }
