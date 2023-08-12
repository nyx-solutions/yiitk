<?php

    namespace yiitk\db\mysql;

    use yii\base\InvalidConfigException;
    use yii\base\NotSupportedException;
    use yiitk\db\Migration as BaseMigration;
    use yiitk\db\migrations\DomainBuilderInterface;
    use yiitk\db\migrations\EnumBuilderInterface;
    use yiitk\db\migrations\EnumBuilderTrait;
    use yiitk\db\SchemaBuilderInterface;
    use yiitk\db\Command;

    /**
     * MySQL Migration
     *
     * @property-read Command $command
     */
    class Migration extends BaseMigration implements SchemaBuilderInterface
    {
        use SchemaBuilderTrait;

        protected const ROW_FORMAT_COMPACT    = 'COMPACT';
        protected const ROW_FORMAT_REDUNDANT  = 'REDUNDANT';
        protected const ROW_FORMAT_DYNAMIC    = 'DYNAMIC';
        protected const ROW_FORMAT_COMPRESSED = 'COMPRESSED';

        /**
         * @var bool
         */
        protected bool $onlyMySql = true;

        /**
         * @var string
         */
        public string $tableCharset = 'utf8';

        /**
         * @var string
         */
        public string $tableCollate = 'utf8_unicode_ci';

        /**
         * @var string
         */
        public string $tableEngine = 'InnoDB';

        /**
         * @var bool
         */
        public bool $useMysqlInnoDbRowFormat = true;

        /**
         * @var bool
         */
        public bool $useMysqlInnoDbBarracudaFileFormat = false;

        /**
         * @var string
         */
        public string $mysqlInnoDbRowFormat = self::ROW_FORMAT_DYNAMIC;

        #region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            $this->useJsonColumn = false;

            if (!$this->isUsingMySqlDriver()) {
                throw new NotSupportedException('MySQL driver required in order to .');
            }

            $rowFormat = '';

            if ($this->useMysqlInnoDbRowFormat && strtolower($this->tableEngine) === 'innodb') {
                $rowFormat = sprintf(' ROW_FORMAT=%s', $this->mysqlInnoDbRowFormat);
            }

            if ($this->useMysqlInnoDbRowFormat && $this->useMysqlInnoDbBarracudaFileFormat && strtolower($this->tableEngine) === 'innodb') {
                $rowFormat = sprintf(' ROW_FORMAT=%s', self::ROW_FORMAT_COMPRESSED);
            }

            $this->tableOptions = "CHARACTER SET {$this->tableCharset} COLLATE {$this->tableCollate}{$rowFormat} ENGINE={$this->tableEngine}";

            parent::init();
        }
        #endregion

        #region Getters
        /**
         * @return Command
         *
         * @throws InvalidConfigException
         */
        public function getCommand(): Command
        {
            $command = $this->db->createCommand();

            if ($command instanceof Command) {
                return $command;
            }

            throw new InvalidConfigException('The Command class must be an instance of '.Command::class);
        }
        #endregion
    }
