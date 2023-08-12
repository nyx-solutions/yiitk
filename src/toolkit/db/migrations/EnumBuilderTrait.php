<?php

    namespace yiitk\db\migrations;

    use yii\helpers\Inflector;
    use yiitk\helpers\StringHelper;

    /**
     * ENUM Builder Trait
     */
    trait EnumBuilderTrait
    {
        /**
         * @inheritdoc
         *
         * @noinspection PhpPossiblePolymorphicInvocationInspection
         */
        public function createEnum(string $class): void
        {
            $name    = $this->normalizeEnumName($class);
            $options = $class::range();

            $values = implode(', ', $options);

            $time = $this->beginCommand("create enum {$name} with values: {$values}");

            $this->db->createCommand()->createEnum($name, $options)->execute();

            $this->endCommand($time);
        }

        /**
         * @inheritdoc
         *
         * @noinspection PhpPossiblePolymorphicInvocationInspection
         */
        public function alterEnum(string $class): void
        {
            $name    = $this->normalizeEnumName($class);
            $options = $class::range();

            $values = implode(', ', $options);

            $time = $this->beginCommand("alter enum {$name} with values {$values}");

            $this->db->createCommand()->alterEnum($name, $options)->execute();

            $this->endCommand($time);
        }

        /**
         * @inheritdoc
         *
         * @noinspection PhpPossiblePolymorphicInvocationInspection
         */
        public function dropEnum(
            string $class,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): void
        {
            $name = $this->normalizeEnumName($class);

            $time = $this->beginCommand("drop enum {$name}");

            $this->db->createCommand()->dropDomain($name, $checkIfExists, $useCascade)->execute();

            $this->endCommand($time);
        }

        /**
         * @param string $class
         *
         * @return string
         */
        protected function normalizeEnumName(string $class): string
        {
            return Inflector::camel2id(StringHelper::basename($class), '_');
        }
    }
