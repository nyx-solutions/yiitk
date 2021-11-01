<?php

    namespace yiitk\enum\base;

    use BadMethodCallException;
    use Closure;
    use ReflectionClass;
    use Throwable;
    use Yii;
    use yii\base\InvalidCallException;
    use yii\base\InvalidConfigException;
    use yii\i18n\PhpMessageSource;
    use yiitk\helpers\ArrayHelper;
    use yiitk\helpers\InflectorHelper;
    use yiitk\helpers\StringHelper;

    /**
     * Base ENUM
     *
     * @property mixed  $value
     * @property mixed  $label
     * @property string $foregroundColor
     * @property string $backgroundColor
     * @property string $icon
     * @property string $iconCssClass
     * @property mixed  $slug
     */
    abstract class BaseEnum implements EnumInterface
    {
        /**
         * @var bool
         */
        public static bool $useI18n = true;

        /**
         * @var array message categories
         */
        public static array $i18nMessageCategories = ['app' => 'app'];

        /**
         * @var string
         */
        public static string $preposition = 'is';

        /**
         * The cached list of constants by name.
         *
         * @var array
         */
        protected static array $keys = [];

        /**
         * The cached list of constants by value.
         *
         * @var array
         */
        protected static array $values = [];

        /**
         * The value managed by this type instance.
         *
         * @var mixed
         */
        protected mixed $currentValue = null;

        /**
         * @var array
         */
        protected array $validations = [];

        #region Constructor

        /**
         * @inheritdoc
         */
        public function __construct($value)
        {
            if (!static::isValidValue($value)) {
                throw new InvalidConfigException("Value '{$value}' is not part of the enum " . static::class);
            }

            $this->currentValue = $value;

            $this->loadValidations();
        }

        /**
         * @inheritdoc
         */
        public static function id()
        {
            $id = (new ReflectionClass(static::class))->getShortName();
            $id = StringHelper::convertCase(InflectorHelper::camel2id($id, '_'), StringHelper::CASE_UPPER);

            return str_replace('_ENUM', '', $id);
        }
        #endregion

        #region Creations
        /**
         * @inheritdoc
         */
        public static function createByKey(string $name): static
        {
            $constants = static::findConstantsByKey();

            if (!array_key_exists($name, $constants)) {
                throw new InvalidConfigException("Name '{$name}' is not exists in the enum constants list " . static::class);
            }

            return new static($constants[$name]);
        }

        /**
         * @inheritdoc
         */
        public static function createByValue($value): static
        {
            if (!empty($value) && !array_key_exists($value, static::findConstantsByValue())) {
                throw new InvalidConfigException("Value '{$value}' is not exists in the enum constants list " . static::class);
            }

            return new static($value);
        }
        #endregion

        #region Default
        /**
         * @inheritdoc
         */
        public static function defaultValue(): int|string|null
        {
            return null;
        }

        /**
         * @inheritdoc
         */
        public static function defaultForegroundColor(): string
        {
            return '#000000';
        }

        /**
         * @inheritdoc
         */
        public static function defaultBackgroundColor(): string
        {
            return '#FFFFFF';
        }

        /**
         * @inheritdoc
         */
        public static function defaultIcon(): string
        {
            return 'fas fa-star';
        }

        /**
         * @inheritdoc
         */
        public static function iconTemplate(): string
        {
            return '<i class="{icon}"></i>';
        }
        #endregion

        #region Listings
        /**
         * @inheritdoc
         */
        public static function listData($exclude = []): array
        {
            $useI18n      = static::$useI18n;
            $i18nCategory = static::findI18nCategory(static::class);

            $labels = [];

            if (!is_array($exclude) || empty($exclude)) {
                $labels = static::findLabels();
            } else {
                foreach (static::findLabels() as $k => $v) {
                    if (!in_array($k, $exclude, true)) {
                        $labels[$k] = $v;
                    }
                }
            }

            if ($useI18n) {
                static::loadI18n();
            }

            return ArrayHelper::getColumn(
                $labels,
                static function ($value) use ($useI18n, $i18nCategory) {
                    return (($useI18n) ? Yii::t($i18nCategory, $value) : $value);
                }
            );
        }

        /**
         * @inheritdoc
         */
        public static function listDataWithDetails(): array
        {
            $items = [];

            foreach (static::findConstantsByKey() as $key => $value) {
                $items[] = [
                    'constant' => $key,
                    'key'      => lcfirst(InflectorHelper::camelize(strtolower($key))),
                    'value'    => $value,
                    'label'    => static::findLabel($value),
                ];
            }

            return $items;
        }

        /**
         * @inheritdoc
         */
        public static function range(): array
        {
            $range = [];

            foreach (static::findConstantsByKey() as $value) {
                $range[] = $value;
            }

            return $range;
        }

        /**
         * @return array
         */
        protected static function findLabels(): array
        {
            $labels = static::labels();

            if (!is_array($labels) || count($labels) <= 0) {
                $labels = [];

                foreach (static::findConstantsByKey() as $value) {
                    $labels[$value] = InflectorHelper::camel2words($value, true);
                }
            }

            return $labels;
        }

        /**
         * @inheritdoc
         */
        public static function labels(): array
        {
            return [];
        }

        /**
         * @inheritdoc
         */
        public static function foregroundColors(): array
        {
            return [];
        }

        /**
         * @inheritdoc
         */
        public static function backgroundColors(): array
        {
            return [];
        }

        /**
         * @inheritdoc
         */
        public static function icons(): array
        {
            return [];
        }

        /**
         * @return array
         */
        protected static function slugs(): array
        {
            return [];
        }
        #endregion

        #region Find
        /**
         * @inheritdoc
         */
        public static function findValueByKey($value): string|int|bool
        {
            return array_search($value, static::listData(), true);
        }

        /**
         * @inheritdoc
         */
        public static function findLabel($value): ?string
        {
            $list         = static::findLabels();
            $i18nCategory = static::findI18nCategory(static::class);

            if (isset($list[$value])) {
                if (static::$useI18n) {
                    static::loadI18n();
                }

                return ((static::$useI18n) ? Yii::t($i18nCategory, $list[$value]) : $list[$value]);
            }

            return null;
        }

        /**
         * @inheritdoc
         */
        public static function findForegroundColor($value): string
        {
            $list = static::foregroundColors();

            if (isset($list[$value])) {
                return (string)$list[$value];
            }

            return static::defaultForegroundColor();
        }

        /**
         * @inheritdoc
         */
        public static function findBackgroundColor($value): string
        {
            $list = static::backgroundColors();

            if (isset($list[$value])) {
                return (string)$list[$value];
            }

            return static::defaultBackgroundColor();
        }

        /**
         * @inheritdoc
         */
        public static function findIconCssClass($value): string
        {
            $list = static::icons();

            if (isset($list[$value])) {
                return (string)$list[$value];
            }

            return static::defaultIcon();
        }

        /**
         * @inheritdoc
         */
        public static function findIcon($value): string
        {
            $list     = static::icons();
            $template = static::iconTemplate();
            $icon     = static::defaultIcon();

            if (isset($list[$value])) {
                $icon = (string)$list[$value];
            }

            return str_replace('{icon}', $icon, $template);
        }

        /**
         * @inheritdoc
         *
         * @noinspection PhpRedundantOptionalArgumentInspection
         */
        public static function findSlug($value): ?string
        {
            $list = static::slugs();

            if (!is_array($list) || count($list) <= 0) {
                $list         = [];
                $i18nCategory = static::findI18nCategory(static::class);

                foreach (static::findLabels() as $key => $label) {

                    if (static::$useI18n) {
                        static::loadI18n();
                    }

                    $list[$key] = InflectorHelper::slug(((static::$useI18n) ? Yii::t($i18nCategory, $label) : $label), '-');
                }
            }

            return $list[$value] ?? null;
        }

        /**
         * @inheritdoc
         */
        public static function findConstantsByKey(): array
        {
            $class = static::class;

            if (!array_key_exists($class, static::$keys)) {
                static::$keys[$class] = (new ReflectionClass($class))->getConstants();
            }

            return static::$keys[$class];
        }

        /**
         * @inheritdoc
         */
        public static function findConstantsByValue(): array
        {
            $class = static::class;

            if (!isset(static::$values[$class])) {
                static::$values[$class] = array_flip(static::findConstantsByKey());
            }

            return static::$values[$class];
        }
        #endregion

        #region Getters
        /**
         * @inheritdoc
         */
        public function getKey()
        {
            $constants = static::findConstantsByValue();

            return $constants[$this->currentValue];
        }

        /**
         * @inheritdoc
         */
        public function getValue(): mixed
        {
            return $this->currentValue;
        }
        #endregion

        #region Verifications
        /**
         * @inheritdoc
         */
        public static function colorable(): bool
        {
            return false;
        }

        /**
         * @inheritdoc
         */
        public static function iconable(): bool
        {
            return false;
        }
        #endregion

        #region i18n
        /**
         * @return void
         *
         * @noinspection PhpRedundantOptionalArgumentInspection
         */
        protected static function loadI18n(): void
        {
            if (!static::$useI18n) {
                return;
            }

            $class = new ReflectionClass(static::class);

            $name = InflectorHelper::camel2id(preg_replace('/^(.*)\.php$/', '$1', basename($class->getFileName())), '-');
            $uid  = "enum/{$name}";

            if (isset(Yii::$app->i18n->translations[$uid])) {
                return;
            }

            $path = dirname($class->getFileName()) . '/messages';
            $file = "{$name}.php";

            if (is_dir($path)) {
                Yii::$app->i18n->translations[$uid] = [
                    'class'          => PhpMessageSource::class,
                    'sourceLanguage' => 'en-US',
                    'basePath'       => $path,
                    'fileMap'        => [$uid => $file],
                ];

                self::$i18nMessageCategories[static::class] = $uid;
            }
        }

        /**
         * @inheritdoc
         */
        public static function findI18nCategory(string $className): string
        {
            if (!isset(self::$i18nMessageCategories[$className])) {
                static::loadI18n();
            }

            return (self::$i18nMessageCategories[$className] ?? 'app');
        }
        #endregion

        #region Validations
        /**
         * @inheritdoc
         */
        public static function isValidKey(string $name): bool
        {
            return array_key_exists($name, static::findConstantsByKey());
        }

        /**
         * @inheritdoc
         */
        public static function isValidValue(string $value): bool
        {
            return (empty($value) || array_key_exists($value, static::findConstantsByValue()));
        }

        #region Magic Validations

        /**
         * @return void
         */
        protected function loadValidations(): void
        {
            foreach ((new ReflectionClass(static::class))->getConstants() as $constantKey => $constantValue) {
                $this->_bind(
                    strtolower(static::$preposition) . InflectorHelper::camelize(strtolower($constantKey)),
                    fn () => ($this->getValue() === $constantValue)
                );

                $this->_bind(
                    lcfirst(InflectorHelper::camelize(strtolower($constantKey))),
                    fn () => $constantValue
                );
            }

            $this->_bind(
                'value',
                fn () => $this->getValue()
            );

            $this->_bind(
                'label',
                fn () => $this::findLabel($this->getValue())
            );

            $this->_bind(
                'foregroundColor',
                fn () => $this::findForegroundColor((string)$this->getValue())
            );

            $this->_bind(
                'backgroundColor',
                fn () => $this::findBackgroundColor((string)$this->getValue())
            );

            $this->_bind(
                'icon',
                fn () => $this::findIcon((string)$this->getValue())
            );

            $this->_bind(
                'iconCssClass',
                fn () => $this::findIconCssClass((string)$this->getValue())
            );

            $this->_bind(
                'slug',
                fn () => $this::findSlug($this->getValue())
            );
        }

        #region Bind

        /**
         * @param string   $name
         * @param callable $method
         */
        private function _bind(string $name, callable $method): void
        {
            $this->validations[$name] = Closure::bind($method, $this, get_class($this));
        }
        #endregion

        #region Magic Methods
        /**
         * @inheritdoc
         */
        public function __get($name)
        {
            if (array_key_exists($name, $this->validations)) {
                return call_user_func($this->validations[$name]);
            }

            return false;
        }

        /**
         * @inheritdoc
         */
        public function __set($name, $value)
        {
            if (array_key_exists($name, $this->validations)) {
                throw new InvalidCallException('You cannot set the read-only Enum property: ' . get_class($this) . '::' . $name);
            }
        }

        /**
         * @inheritdoc
         */
        public function __unset($name)
        {
            if (array_key_exists($name, $this->validations)) {
                throw new InvalidCallException('You cannot unset the read-only Enum property: ' . get_class($this) . '::' . $name);
            }
        }

        /**
         * @inheritdoc
         */
        public function __isset($name)
        {
            return array_key_exists($name, $this->validations);
        }
        #endregion
        #endregion
        #endregion

        #region Magic Methods
        /**
         * @inheritdoc
         */
        public static function __callStatic($name, $arguments)
        {
            $constants = static::findConstantsByKey();

            $name = strtoupper(InflectorHelper::camel2id($name, '_'));

            if (isset($constants[$name])) {
                return new static($constants[$name]);
            }

            throw new BadMethodCallException("No static method or enum constant '{$name}' in class " . static::class);
        }

        /**
         * @inheritdoc
         */
        public function __toString()
        {
            return (string)$this->currentValue;
        }

        /**
         * @inheritdoc
         */
        public function __debugInfo()
        {
            return [
                'value'   => $this->currentValue,
                'label'   => $this->label,
                'options' => static::listData(),
            ];
        }
        #endregion

        #region Helpers
        /**
         * @inheritdoc
         */
        public static function guess(mixed $value, mixed $default = null): static
        {
            if (is_string($default)) {
                if (in_array($default, static::range(), true)) {
                    $default = static::createByValue($default);
                } else {
                    $default = null;
                }
            }

            if ($default === null) {
                $default = static::defaultValue();

                if ($default === null) {
                    throw new InvalidConfigException('The default value cannot be null.');
                }

                $default = static::createByValue($default);
            }

            try {
                if ($value instanceof static) {
                    return $value;
                }

                if (is_string($value) && in_array($value, static::range(), true)) {
                    return static::createByValue($value);
                }
            } catch (Throwable) {
            }


            return $default;
        }
        #endregion
    }
