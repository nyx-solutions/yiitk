<?php

    namespace yiitk\enum\base;

    /**
     * Interface: ENUM
     */
    interface EnumInterface
    {
        #region Constructor
        /**
         * Sets the value that will be managed by this type instance.
         *
         * @param mixed $value The value to be managed
         */
        public function __construct($value);

        /**
         * The current class ID
         */
        public static function id();
        #endregion

        #region Creations
        /**
         * Creates a new type instance using the name of a value.
         *
         * @param string $name The name of a value
         *
         * @return static The new type instance
         */
        public static function createByKey(string $name): static;

        /**
         * Creates a new type instance using the value.
         *
         * @param mixed $value The value
         *
         * @return $this The new type instance
         */
        public static function createByValue($value): static;
        #endregion

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
         * Get list data (value => label)
         *
         * @param array $exclude
         *
         * @return array
         */
        public static function listData($exclude = []): array;

        /**
         * Get list data (['key' => value, 'label' => label])
         *
         * @return array
         */
        public static function listDataWithDetails(): array;

        /**
         * @return array
         */
        public static function range(): array;

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

        #region Find
        /**
         * get constant key by value(label)
         *
         * @param mixed $value
         *
         * @return string|int|bool
         */
        public static function findValueByKey($value): string|int|bool;

        /**
         * Get label by value
         *
         * @param string value
         *
         * @return string|null label
         */
        public static function findLabel($value): ?string;

        /**
         * @param $value
         *
         * @return string
         */
        public static function findForegroundColor($value): string;

        /**
         * @param $value
         *
         * @return string
         */
        public static function findBackgroundColor($value): string;

        /**
         * @param $value
         *
         * @return string
         */
        public static function findIconCssClass($value): string;

        /**
         * @param $value
         *
         * @return string
         */
        public static function findIcon($value): string;

        /**
         * Get label by value
         *
         * @param string value
         *
         * @return string|null label
         */
        public static function findSlug($value): ?string;

        /**
         * Returns the list of constants (by name) for this type.
         *
         * @return array The list of constants by name
         */
        public static function findConstantsByKey(): array;

        /**
         * Returns the list of constants (by value) for this type.
         *
         * @return array The list of constants by value
         */
        public static function findConstantsByValue(): array;
        #endregion

        #region Getters
        /**
         * Returns the name of the value.
         *
         * @return array|string The name, or names, of the value
         */
        public function getKey();

        /**
         * Unwraps the type and returns the raw value.
         *
         * @return mixed The raw value managed by the type instance
         */
        public function getValue(): mixed;
        #endregion

        #region Verifications
        /**
         * @return bool
         */
        public static function colorable(): bool;

        /**
         * @return bool
         */
        public static function iconable(): bool;
        #endregion

        #region i18n
        /**
         * @param string $className
         *
         * @return string
         */
        public static function findI18nCategory(string $className): string;
        #endregion

        #region Validations
        /**
         * Checks if a name is valid for this type.
         *
         * @param string $name The name of the value
         *
         * @return bool If the name is valid for this type, `true` is returned.
         * Otherwise, the name is not valid and `false` is returned
         */
        public static function isValidKey(string $name): bool;

        /**
         * Checks if a value is valid for this type.
         *
         * @param string $value The value
         *
         * @return bool If the value is valid for this type, `true` is returned.
         * Otherwise, the value is not valid and `false` is returned
         */
        public static function isValidValue(string $value): bool;


        #region Magic Methods
        /**
         * @param string $name
         *
         * @return bool
         */
        public function __get($name);

        /**
         * @param string $name
         * @param mixed  $value
         */
        public function __set($name, $value);

        /**
         * @param string $name
         */
        public function __unset($name);

        /**
         * @param string $name
         *
         * @return bool
         */
        public function __isset($name);
        #endregion
        #endregion

        #region Helpers
        /**
         * @param mixed      $value
         * @param mixed|null $default
         *
         * @return static
         */
        public static function guess(mixed $value, mixed $default = null): static;
        #endregion

        #region Magic Methods
        /**
         * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
         *
         * @param string $name
         * @param array  $arguments
         *
         * @return static
         *
         * @noinspection PhpMissingParamTypeInspection
         */
        public static function __callStatic($name, $arguments);

        /**
         * @return string
         */
        public function __toString();

        /**
         * @return array
         */
        public function __debugInfo();
        #endregion
    }
