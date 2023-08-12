<?php

    namespace yiitk\db;

    use yiitk\db\mysql\Schema as MySqlSchema;
    use yiitk\db\pgsql\Schema as PgSqlSchema;

    /**
     * Connection
     */
    class Connection extends \yii\db\Connection
    {
        #region Initialization
        /**
         * @inheritdoc
         */
        public function init()
        {
            if (isset($this->schemaMap['pgsql']) && is_array($this->schemaMap['pgsql']) && array_key_exists('class', $this->schemaMap['pgsql'])) {
                $this->schemaMap['pgsql']['class'] = PgSqlSchema::class;
            } else {
                $this->schemaMap['pgsql'] = PgSqlSchema::class;
            }

            if (isset($this->schemaMap['mysql']) && is_array($this->schemaMap['mysql']) && array_key_exists('class', $this->schemaMap['mysql'])) {
                $this->schemaMap['mysql']['class'] = MySqlSchema::class;
            } else {
                $this->schemaMap['mysql'] = MySqlSchema::class;
            }

            if (isset($this->schemaMap['mysqli']) && is_array($this->schemaMap['mysqli']) && array_key_exists('class', $this->schemaMap['mysqli'])) {
                $this->schemaMap['mysqli']['class'] = MySqlSchema::class;
            } else {
                $this->schemaMap['mysqli'] = MySqlSchema::class;
            }

            parent::init();
        }
        #endregion
    }
