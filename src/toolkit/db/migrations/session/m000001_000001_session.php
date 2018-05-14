<?php

    namespace yiitk\db\migrations\session;

    use yiitk\db\Migration;
    use yiitk\Module;

    /**
     * Migration: Sessão
     */
    class m000001_000001_session extends Migration
    {
        /**
         * @var string
         */
        public $tableEngine = 'MyISAM';

        /**
         * @var bool
         */
        public $useMysqlInnoDbRowFormat = false;

        /**
         * @var bool
         */
        public $useMysqlInnoDbBarracudaFileFormat = false;

        /**
         * {@inheritdoc}
         */
        public function safeUp()
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

            if ($yiitk->useSessionDb) {
                $this->createTable($sessionTable, $columns, $this->getTableOptions());
                $this->addPrimaryKey('SESSION_PK', $sessionTable, 'id');
            }

            if ($yiitk->useSessionDbBackend) {
                $this->createTable($sessionBackendTable, $columns, $this->getTableOptions());
                $this->addPrimaryKey('SESSION_PK', $sessionBackendTable, 'id');
            }

            if ($yiitk->useSessionDbFrontend) {
                $this->createTable($sessionFrontendTable, $columns, $this->getTableOptions());
                $this->addPrimaryKey('SESSION_PK', $sessionFrontendTable, 'id');
            }

            if ($yiitk->useSessionDbApi) {
                $this->createTable($sessionApiTable, $columns, $this->getTableOptions());
                $this->addPrimaryKey('SESSION_PK', $sessionApiTable, 'id');
            }
        }
    }
