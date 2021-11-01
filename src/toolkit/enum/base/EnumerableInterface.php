<?php

    namespace yiitk\enum\base;

    /**
     * Interface: Enumerable
     */
    interface EnumerableInterface
    {
        #region Default
        /**
         * @return int|string|null
         */
        public static function defaultValue(): int|string|null;

        /**
         * @return string
         */
        public static function defaultForegroundColor(): string;

        /**
         * @return string
         */
        public static function defaultBackgroundColor(): string;

        /**
         * @return string
         */
        public static function defaultIcon(): string;

        /**
         * @return string
         */
        public static function iconTemplate(): string;
        #endregion

        #region Listings
        /**
         * @return array
         */
        public static function labels(): array;

        /**
         * @return array
         */
        public static function foregroundColors(): array;

        /**
         * @return array
         */
        public static function backgroundColors(): array;

        /**
         * @return array
         */
        public static function icons(): array;
        #endregion
    }
