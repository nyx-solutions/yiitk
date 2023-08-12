<?php

    namespace yiitk\db\migrations;

    use yii\db\Expression;

    /**
     * Interface: Domain Builder
     */
    interface DomainBuilderInterface
    {
        /**
         * @param string                           $name
         * @param string                           $dataType
         * @param bool                             $nullable
         * @param string|int|float|Expression|null $defaultExpression
         * @param string|null                      $collation
         * @param string|null                      $constraintName
         * @param Expression|null                  $checkExpression
         *
         * @return void
         */
        public function createDomain(
            string                           $name,
            string                           $dataType,
            bool                             $nullable = true,
            string|int|float|Expression|null $defaultExpression = null,
            ?string                          $collation = null,
            ?string                          $constraintName = null,
            ?Expression                      $checkExpression = null
        ): void;

        /**
         * @param string $name
         * @param bool   $checkIfExists
         * @param bool   $useCascade
         *
         * @return void
         */
        public function dropDomain(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): void;
    }
