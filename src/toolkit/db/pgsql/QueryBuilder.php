<?php

    namespace yiitk\db\pgsql;

    use yiitk\db\QueryBuilderInterface;
    use yiitk\db\QueryBuilderTrait;
    use yii\db\Expression;
    use yii\db\pgsql\QueryBuilder as YiiQueryBuilder;

    /**
     * Query Builder
     */
    class QueryBuilder extends YiiQueryBuilder implements QueryBuilderInterface
    {
        use QueryBuilderTrait;

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
        ): string
        {
            if ($constraintName === null) {
                $constraintName = "{$name}_rule";
            }

            $name         = $this->db->quoteTableName($name);
            $collationSql = '';
            $defaultSql   = '';
            $nullSql      = ' NULL';
            $checkSql     = '';

            if (!$nullable) {
                $nullSql = ' NOT NULL';
            }

            if ($collation !== null) {
                $collationSql = " COLLATE {$collation}";
            }

            if ($defaultExpression !== null) {
                $defaultSql = sprintf(' DEFAULT %s', $this->parseSqlExpression($defaultExpression));
            }

            if ($checkExpression !== null) {
                $checkSql = sprintf(' CHECK (%s)', $this->parseSqlExpression($checkExpression));
            }

            return sprintf(
                'CREATE DOMAIN %s AS %s%s%s CONSTRAINT %s%s%s',
                $name,
                $dataType,
                $collationSql,
                $defaultSql,
                $constraintName,
                $nullSql,
                $checkSql
            );
        }

        /**
         * @inheritdoc
         */
        public function dropDomain(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): string
        {
            return sprintf(
                'DROP DOMAIN%s %s%s',
                (($checkIfExists) ? ' IF EXISTS' : ''),
                $this->db->quoteTableName($name),
                (($useCascade) ? ' CASCADE' : ' RESTRICT')
            );
        }
        #endregion

        #region ENUM
        /**
         * @inheritdoc
         */
        public function createEnum(
            string $name,
            array  $options
        ): string
        {
            $name = $this->db->quoteTableName($name);

            $filteredOptions = implode(', ', array_map(
                static fn ($option) => "'{$option}'",
                $options
            ));

            return sprintf(
                'CREATE TYPE %s AS ENUM (%s)',
                $name,
                $filteredOptions,
            );
        }

        /**
         * @inheritdoc
         */
        public function alterEnum(
            string $name,
            array  $options
        ): string
        {
            return '';
        }

        /**
         * @inheritdoc
         */
        public function dropEnum(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): string
        {
            return sprintf(
                'DROP TYPE%s %s%s',
                (($checkIfExists) ? ' IF EXISTS' : ''),
                $this->db->quoteTableName($name),
                (($useCascade) ? ' CASCADE' : ' RESTRICT')
            );
        }
        #endregion
    }
