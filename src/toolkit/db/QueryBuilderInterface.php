<?php

    namespace yiitk\db;

    use yii\db\Expression;

    /**
     * Interface: Query Builder
     */
    interface QueryBuilderInterface
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
         * @return string
         */
        public function createDomain(
            string                           $name,
            string                           $dataType,
            bool                             $nullable = true,
            string|int|float|Expression|null $defaultExpression = null,
            ?string                          $collation = null,
            ?string                          $constraintName = null,
            ?Expression                      $checkExpression = null
        ): string;

        /**
         * @param string $name
         * @param bool   $checkIfExists
         * @param bool   $useCascade
         *
         * @return string
         */
        public function dropDomain(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): string;
        #endregion

        #region ENUM
        /**
         * @param string $name
         * @param array  $options
         *
         * @return string
         */
        public function createEnum(
            string $name,
            array  $options
        ): string;

        /**
         * @param string $name
         * @param array  $options
         *
         * @return string
         */
        public function alterEnum(
            string $name,
            array  $options
        ): string;

        /**
         * @param string $name
         * @param bool   $checkIfExists
         * @param bool   $useCascade
         *
         * @return string
         */
        public function dropEnum(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): string;
        #endregion
    }
