<?php

    namespace yiitk\db;

    use yii\db\Expression;

    /**
     * Interface: Command
     */
    interface CommandInterface
    {
        #region Domains
        /**
         * @param string                           $name
         * @param string                           $dataType
         * @param bool                             $nullable
         * @param string|int|float|Expression|null $defaultExpression
         * @param string|null                      $collation
         * @param string|null                      $constraintName
         * @param Expression|null                  $checkExpression
         *
         * @return static
         */
        public function createDomain(
            string                           $name,
            string                           $dataType,
            bool                             $nullable = true,
            string|int|float|Expression|null $defaultExpression = null,
            ?string                          $collation = null,
            ?string                          $constraintName = null,
            ?Expression                      $checkExpression = null
        ): static;

        /**
         * @param string $name
         * @param bool   $checkIfExists
         * @param bool   $useCascade
         *
         * @return static
         */
        public function dropDomain(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): static;
        #endregion

        #region ENUM
        /**
         * @param string $name
         * @param array  $options
         *
         * @return static
         */
        public function createEnum(
            string $name,
            array  $options
        ): static;

        /**
         * @param string $name
         * @param array  $options
         *
         * @return static
         */
        public function alterEnum(
            string $name,
            array  $options
        ): static;

        /**
         * @param string $name
         * @param bool   $checkIfExists
         * @param bool   $useCascade
         *
         * @return static
         */
        public function dropEnum(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): static;
        #endregion
    }
