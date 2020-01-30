<?php

    namespace yiitk\enum\base;

    use BadMethodCallException;
    use Closure;
    use ReflectionClass;
    use ReflectionException;
    use Yii;
    use yii\base\InvalidCallException;
    use yii\base\InvalidConfigException;
    use yii\i18n\PhpMessageSource;
    use yiitk\helpers\ArrayHelper;
    use yiitk\helpers\InflectorHelper;
    use yiitk\helpers\StringHelper;

    /**
     * Class BaseEnum
     *
     * @property mixed $value
     * @property mixed $label
     * @property mixed $slug
     */
    class BaseEnum
    {
        /**
         * @var bool
         */
        public static $useI18n = true;

        /**
         * @var array message categories
         */
        public static $i18nMessageCategories = ['app' => 'app'];

        /**
         * @var string
         */
        public static $preposition = 'is';

        /**
         * The cached list of constants by name.
         *
         * @var array
         */
        protected static $keys = [];

        /**
         * The cached list of constants by value.
         *
         * @var array
         */
        protected static $values = [];

        /**
         * The value managed by this type instance.
         *
         * @var mixed
         */
        protected $currentValue;

        /**
         * @var array
         */
        protected $validations = [];

        #region Constructor
        /**
         * Sets the value that will be managed by this type instance.
         *
         * @param mixed $value The value to be managed
         *
         * @throws InvalidConfigException If the value is not valid
         * @throws ReflectionException
         */
        public function __construct($value)
        {
            if (!static::isValidValue($value)) {
                throw new InvalidConfigException("Value '{$value}' is not part of the enum ".get_called_class());
            }

            $this->currentValue = $value;

            $this->loadValidations();
        }

        /**
         * The current class ID
         */
        public static function id()
        {
            $id = (new ReflectionClass(static::class))->getShortName();
            $id = StringHelper::convertCase(InflectorHelper::camel2id($id, '_'), StringHelper::CASE_UPPER);
            $id = str_replace('_ENUM', '', $id);

            return $id;
        }
        #endregion

        #region Creations
        /**
         * Creates a new type instance using the name of a value.
         *
         * @param string $name The name of a value
         *
         * @return $this The new type instance
         *
         * @throws InvalidConfigException
         * @throws ReflectionException
         */
        public static function createByKey($name)
        {
            $constants = static::findConstantsByKey();

            if (!array_key_exists($name, $constants)) {
                throw new InvalidConfigException("Name '{$name}' is not exists in the enum constants list ".get_called_class());
            }

            return new static($constants[$name]);
        }

        /**
         * Creates a new type instance using the value.
         *
         * @param mixed $value The value
         *
         * @return $this The new type instance
         *
         * @throws InvalidConfigException
         * @throws ReflectionException
         */
        public static function createByValue($value)
        {
            if (!is_null($value) && !empty($value) && !array_key_exists($value, static::findConstantsByValue())) {
                throw new InvalidConfigException("Value '{$value}' is not exists in the enum constants list ".get_called_class());
            }

            return new static($value);
        }
        #endregion

        #region Default
        /**
         * @return mixed
         */
        public static function defaultValue()
        {
            return null;
        }
        #endregion

        #region Listings
        /**
         * Get list data (value => label)
         *
         * @return mixed
         *
         * @throws ReflectionException
         */
        public static function listData()
        {
            $useI18n      = static::$useI18n;
            $i18nCategory = static::findI18nCategory(get_called_class());

            if ($useI18n) {
                static::loadI18n();
            }

            return ArrayHelper::getColumn(
                static::findLabels(),
                function ($value) use ($useI18n, $i18nCategory) {
                    return (($useI18n) ? Yii::t($i18nCategory, $value) : $value);
                }
            );
        }

        /**
         * Get list data (['key' => value, 'label' => label])
         *
         * @return mixed
         *
         * @throws ReflectionException
         */
        public static function listDataWithDetails()
        {
            $items = [];

            foreach (static::findConstantsByKey() as $key => $value) {
                $items[] = [
                    'constant' => $key,
                    'key'      => lcfirst(InflectorHelper::camelize(strtolower($key))),
                    'value'    => $value,
                    'label'    => static::findLabel($value)
                ];
            }

            return $items;
        }

        /**
         * @return array
         *
         * @throws ReflectionException
         */
        public static function range()
        {
            $range = [];

            foreach (static::findConstantsByKey() as $value) {
                $range[] = $value;
            }

            return $range;
        }

        /**
         * @return array
         *
         * @throws ReflectionException
         */
        protected static function findLabels()
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
         * @return array
         */
        protected static function labels()
        {
            return [];
        }

        /**
         * @return array
         */
        protected static function slugs()
        {
            return [];
        }
        #endregion

        #region Find
        /**
         * get constant key by value(label)
         *
         * @param mixed $value
         *
         * @return mixed
         *
         * @throws ReflectionException
         */
        public static function findValueByKey($value)
        {
            return array_search($value, static::listData());
        }

        /**
         * Get label by value
         *
         * @param string value
         *
         * @return string label
         *
         * @throws ReflectionException
         */
        public static function findLabel($value)
        {
            $list         = static::findLabels();
            $i18nCategory = static::findI18nCategory(get_called_class());

            if (isset($list[$value])) {
                if (static::$useI18n) {
                    static::loadI18n();
                }

                return ((static::$useI18n) ? Yii::t($i18nCategory, $list[$value]) : $list[$value]);
            }

            return null;
        }

        /**
         * Get label by value
         *
         * @param string value
         *
         * @return string label
         *
         * @throws ReflectionException
         */
        public static function findSlug($value)
        {
            $list = static::slugs();

            if (!is_array($list) || count($list) <= 0) {
                $list         = [];
                $i18nCategory = static::findI18nCategory(get_called_class());

                foreach (static::findLabels() as $key => $label) {

                    if (static::$useI18n) {
                        static::loadI18n();
                    }

                    $list[$key] = InflectorHelper::slug(((static::$useI18n) ? Yii::t($i18nCategory, $label) : $label), '-');
                }
            }

            if (isset($list[$value])) {
                return $list[$value];
            }

            return null;
        }

        /**
         * Returns the list of constants (by name) for this type.
         *
         * @return array The list of constants by name
         *
         * @throws ReflectionException
         */
        public static function findConstantsByKey()
        {
            $class = get_called_class();

            if (!array_key_exists($class, static::$keys)) {
                $reflection = new ReflectionClass($class);

                static::$keys[$class] = $reflection->getConstants();
            }

            return static::$keys[$class];
        }

        /**
         * Returns the list of constants (by value) for this type.
         *
         * @return array The list of constants by value
         *
         * @throws ReflectionException
         */
        public static function findConstantsByValue()
        {
            $class = get_called_class();

            if (!isset(static::$values[$class])) {
                static::$values[$class] = array_flip(static::findConstantsByKey());
            }

            return static::$values[$class];
        }
        #endregion

        #region Getters
        /**
         * Returns the name of the value.
         *
         * @return array|string The name, or names, of the value
         *
         * @throws ReflectionException
         */
        public function getKey()
        {
            $constants = static::findConstantsByValue();

            return $constants[$this->currentValue];
        }

        /**
         * Unwraps the type and returns the raw value.
         *
         * @return mixed The raw value managed by the type instance
         */
        public function getValue()
        {
            return $this->currentValue;
        }
        #endregion

        #region i18n
        /**
         * @throws ReflectionException
         */
        protected static function loadI18n()
        {
            if (!static::$useI18n) {
                return;
            }

            $class = new ReflectionClass(get_called_class());

            $name = InflectorHelper::camel2id(preg_replace('/^(.*)\.php$/', '$1', basename($class->getFileName())), '-');
            $uid  = "enum/{$name}";

            if (isset(Yii::$app->i18n->translations[$uid])) {
                return;
            }

            $path = dirname($class->getFileName()).'/messages';
            $file = "{$name}.php";

            if (is_dir($path)) {
                Yii::$app->i18n->translations[$uid] = [
                    'class'          => PhpMessageSource::class,
                    'sourceLanguage' => 'en-US',
                    'basePath'       => $path,
                    'fileMap'        => [$uid => $file]
                ];

                self::$i18nMessageCategories[get_called_class()] = $uid;
            }
        }

        /**
         * @param string $className
         *
         * @return string
         *
         * @throws ReflectionException
         */
        public static function findI18nCategory($className)
        {
            if (!isset(self::$i18nMessageCategories[$className])) {
                static::loadI18n();
            }

            return ((isset(self::$i18nMessageCategories[$className])) ? self::$i18nMessageCategories[$className] : 'app');
        }
        #endregion

        #region Validations
        /**
         * Checks if a name is valid for this type.
         *
         * @param string $name The name of the value
         *
         * @return bool If the name is valid for this type, `true` is returned.
         * Otherwise, the name is not valid and `false` is returned
         *
         * @throws ReflectionException
         */
        public static function isValidKey($name)
        {
            return array_key_exists($name, static::findConstantsByKey());
        }

        /**
         * Checks if a value is valid for this type.
         *
         * @param string $value The value
         *
         * @return bool If the value is valid for this type, `true` is returned.
         * Otherwise, the value is not valid and `false` is returned
         *
         * @throws ReflectionException
         */
        public static function isValidValue($value)
        {
            return (is_null($value) || empty($value) || array_key_exists($value, static::findConstantsByValue()));
        }

        #region Magic Validations
        /**
         * @throws ReflectionException
         */
        protected function loadValidations()
        {
            $class = new ReflectionClass(get_called_class());

            foreach ($class->getConstants() as $constantKey => $constantValue) {
                $this->_bind(
                    strtolower(static::$preposition).InflectorHelper::camelize(strtolower($constantKey)),
                    function () use ($constantValue) {
                        return ($this->getValue() == $constantValue);
                    }
                );

                $this->_bind(
                    lcfirst(InflectorHelper::camelize(strtolower($constantKey))),
                    function () use ($constantValue) {
                        return $constantValue;
                    }
                );
            }

            $this->_bind(
                'value',
                function () {
                    return $this->getValue();
                }
            );

            $this->_bind(
                'label',
                function () {
                    return $this::findLabel($this->getValue());
                }
            );

            $this->_bind(
                'slug',
                function () {
                    return $this::findSlug($this->getValue());
                }
            );
        }

        #region Bind

        /**
         * @param string   $name
         * @param Closure $method
         */
        private function _bind($name, $method)
        {
            $this->validations[$name] = Closure::bind($method, $this, get_class($this));
        }
        #endregion

        #region Magic Methods
        /**
         * @param string $name
         *
         * @return bool
         */
        public function __get($name)
        {
            if (array_key_exists($name, $this->validations)) {
                return call_user_func($this->validations[$name]);
            }

            return false;
        }

        /**
         * @param string $name
         * @param mixed  $value
         */
        public function __set($name, $value)
        {
            if (array_key_exists($name, $this->validations)) {
                throw new InvalidCallException('You cannot set the read-only Enum property: '.get_class($this).'::'.$name);
            }
        }

        /**
         * @param string $name
         */
        public function __unset($name)
        {
            if (array_key_exists($name, $this->validations)) {
                throw new InvalidCallException('You cannot unset the read-only Enum property: '.get_class($this).'::'.$name);
            }
        }

        /**
         * @param string $name
         *
         * @return bool
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
         * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
         *
         * @param string $name
         * @param array  $arguments
         *
         * @return static
         *
         * @throws InvalidConfigException
         * @throws ReflectionException
         */
        public static function __callStatic($name, $arguments)
        {
            $constants = static::findConstantsByKey();

            $name = strtoupper(InflectorHelper::camel2id($name, '_'));

            if (isset($constants[$name])) {
                return new static($constants[$name]);
            }

            throw new BadMethodCallException("No static method or enum constant '{$name}' in class ".get_called_class());
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return (string)$this->currentValue;
        }

        /**
         * @return array
         *
         * @throws ReflectionException
         */
        public function __debugInfo()
        {
            return [
                'value'   => $this->currentValue,
                'label'   => $this->label,
                'options' => static::listData()
            ];
        }
        #endregion
    }
