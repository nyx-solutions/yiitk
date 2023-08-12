<?php

    namespace yiitk\db;

    use yii\db\Command as YiiCommand;
    use yii\db\Expression;
    use yii\db\QueryBuilder;
    use yiitk\db\mysql\QueryBuilder as MySqlQueryBuilder;
    use yiitk\db\pgsql\QueryBuilder as PgSqlQueryBuilder;

    /**
     * Command
     */
    class Command extends YiiCommand implements CommandInterface
    {
        #region Domains
        /**
         * @inheritdoc
         */
        public function createDomain(
            string                           $name,
            string                           $dataType,
            bool                             $nullable = true,
            string|int|float|Expression|null $defaultExpression = null,
            ?string                          $collation = null,
            ?string                          $constraintName = null,
            ?Expression                      $checkExpression = null
        ): static
        {
            $sql = $this->_queryBuilder()->createDomain($name, $dataType, $nullable, $defaultExpression, $collation, $constraintName, $checkExpression);

            return $this->setSql($sql);
        }

        /**
         * @inheritdoc
         */
        public function dropDomain(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): static
        {
            $sql = $this->_queryBuilder()->dropDomain($name, $checkIfExists, $useCascade);

            return $this->setSql($sql);
        }
        #endregion

        #region ENUM
        /**
         * @inheritdoc
         */
        public function createEnum(
            string $name,
            array  $options
        ): static
        {
            $sql = $this->_queryBuilder()->createEnum($name, $options);

            return $this->setSql($sql);
        }

        /**
         * @inheritdoc
         */
        public function alterEnum(
            string $name,
            array  $options
        ): static
        {
            $sql = $this->_queryBuilder()->alterEnum($name, $options);

            return $this->setSql($sql);
        }

        /**
         * @inheritdoc
         */
        public function dropEnum(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): static
        {
            $sql = $this->_queryBuilder()->dropEnum($name, $checkIfExists, $useCascade);

            return $this->setSql($sql);
        }
        #endregion

        #region Helpers
        /**
         * @return MySqlQueryBuilder|PgSqlQueryBuilder
         *
         * @noinspection PhpDocSignatureInspection
         */
        private function _queryBuilder(): QueryBuilder
        {
            return $this->db->getQueryBuilder();
        }
        #endregion
    }
