<?php

    namespace yiitk\db;

    use yii\base\NotSupportedException;
    use yii\db\Expression;

    /**
     * Query Builder Trait
     */
    trait QueryBuilderTrait
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
        ): string
        {
            $this->throwNotSupportedException(__METHOD__);
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
            $this->throwNotSupportedException(__METHOD__);
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
            $this->throwNotSupportedException(__METHOD__);
        }

        /**
         * @inheritdoc
         */
        public function alterEnum(
            string $name,
            array  $options
        ): string
        {
            $this->throwNotSupportedException(__METHOD__);
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
            $this->throwNotSupportedException(__METHOD__);
        }
        #endregion

        #region Helpers
        /**
         * @param string|int|float|Expression|null $expression
         * @param string                           $empty
         *
         * @return string
         */
        protected function parseSqlExpression(string|int|float|Expression|null $expression = null, string $empty = ''): string
        {
            if ($expression === null) {
                return $empty;
            } elseif ($expression instanceof Expression) {
                return (string)$expression;
            } elseif (is_string($expression)) {
                return sprintf('"%s"', $expression);
            }

            return $expression;
        }

        /**
         * @param string $method
         *
         * @return void
         *
         * @throws NotSupportedException
         */
        protected function throwNotSupportedException(string $method): void
        {
            throw new NotSupportedException(sprintf('The current DB driver %s does not support the %s column.', $this->db->driverName, $method));
        }
        #endregion
    }
