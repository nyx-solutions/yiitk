<?php

    namespace yiitk\db\migrations;

    /**
     * Interface: ENUM Builder
     */
    interface EnumBuilderInterface
    {
        /**
         * @param string $class
         *
         * @return void
         */
        public function createEnum(string $class): void;

        /**
         * @param string $class
         *
         * @return void
         */
        public function alterEnum(string $class): void;

        /**
         * @param string $class
         * @param bool   $checkIfExists
         * @param bool   $useCascade
         *
         * @return void
         */
        public function dropEnum(
            string $class,
            bool $checkIfExists = true,
            bool $useCascade = true
        ): void;
    }
