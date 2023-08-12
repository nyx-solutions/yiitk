<?php

    namespace yiitk\db\migrations;

    use yii\db\Expression;

    /**
     * Domain Builder Trait
     */
    trait DomainBuilderTrait
    {
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
        ): void
        {
            $time = $this->beginCommand("create domain {$name} as {$dataType}");

            $this->getCommand()->createDomain($name, $dataType, $nullable, $defaultExpression, $collation, $constraintName, $checkExpression)->execute();

            $this->endCommand($time);
        }

        /**
         * @inheritdoc
         */
        public function dropDomain(
            string $name,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): void
        {
            $time = $this->beginCommand("drop domain {$name}");

            $this->getCommand()->dropDomain($name, $checkIfExists, $useCascade)->execute();

            $this->endCommand($time);
        }
    }
