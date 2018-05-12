<?php

    namespace yiitk\enum\base;

    use yiitk\helpers\InflectorHelper;
    use yii\base\Event;
    use yii\base\InvalidCallException;
    use yii\db\BaseActiveRecord;

    /**
     * Trait EnumTrait
     */
    trait EnumTrait
    {
        /**
         * @var array
         */
        private $_enumMethods = [];

        /**
         * @var array
         */
        private $_enumMap = [];

        /**
         * @var array
         */
        private $_enumAttributes = [];

        #region Initialization
        /**
         * {@inheritdoc}
         */
        public function init()
        {
            $this->_enumBind();

            if ($this instanceof BaseActiveRecord) {
                $attributes = $this->_enumMap;

                foreach ($attributes as $key => $enum) {
                    $class = $enum['class'];
                    $default = $enum['default'];

                    /** @var BaseEnum $class */
                    if (is_null($default)) {
                        $default = $class::createByValue($class::defaultValue());
                    }

                    if ($default instanceof BaseEnum) {
                        $this->setEnumAttribute($key, $default);
                    }
                }

                $findEvent = function ($event) use ($attributes) {
                    /** @var Event $event */
                    /** @var BaseActiveRecord $sender */
                    $sender = $event->sender;

                    if ($sender instanceof BaseActiveRecord) {
                        /** @var BaseEnum $class */
                        foreach ($attributes as $key => $enum) {
                            $class = $enum['class'];

                            $sender->$key = $class::createByValue($sender->getAttribute($key));
                        }
                    }
                };

                $saveEvent = function ($event) use ($attributes) {
                    /** @var Event $event */
                    /** @var BaseActiveRecord $sender */
                    $sender = $event->sender;

                    if ($sender instanceof BaseActiveRecord) {
                        /** @var BaseEnum $class */
                        foreach ($attributes as $key => $enum) {
                            $class = $enum['class'];

                            if ($sender->$key instanceof BaseEnum) {
                                $sender->setAttribute($key, $sender->$key->__toString());
                            }
                        }
                    }
                };

                $this->on(BaseActiveRecord::EVENT_AFTER_FIND,    $findEvent);
                $this->on(BaseActiveRecord::EVENT_BEFORE_INSERT, $saveEvent);
                $this->on(BaseActiveRecord::EVENT_BEFORE_UPDATE, $saveEvent);
            }

            if (is_callable(['parent', 'init'])) {
                /** @noinspection PhpUndefinedClassInspection */
                parent::init();
            }
        }
        #endregion

        #region Enum
        /**
         * @return array
         */
        public function enums()
        {
            return [];
        }
        #endregion

        #region Bind Methods
        /**
         * @throws \ReflectionException
         */
        private function _enumBind()
        {
            if (count($this->_enumMethods) > 0) {
                return;
            }

            $enums = $this->enums();

            $attributes = [];
            $defaults   = [];

            if (is_array($enums) && count($enums) > 0) {
                foreach ($enums as $enum) {
                    if (is_array($enum) && isset($enum[0], $enum['enumClass'])) {
                        if (is_string($enum[0])) {
                            $attributes[$enum[0]] = $enum['enumClass'];
                            $defaults[$enum[0]]   = ((isset($enum['default']) && $enum['default'] instanceof BaseEnum) ? $enum['default'] : null);
                        } elseif (is_array($enum[0])) {
                            foreach ($enum[0] as $attribute) {
                                $attributes[$attribute] = $enum['enumClass'];
                                $defaults[$attribute]   = ((isset($enum['default']) && $enum['default'] instanceof BaseEnum) ? $enum['default'] : null);
                            }
                        }
                    }
                }
            }

            foreach ($attributes as $key => $enum) {
                $class = new \ReflectionClass($enum);

                if ($class->isSubclassOf(BaseEnum::class)) {
                    $this->_enumMap[$key] = ['class' => $enum, 'default' => $defaults[$key]];

                    $attribute = InflectorHelper::camelize($key);

                    $getter = $this->_enumGetter($attribute);
                    $setter = $this->_enumSetter($attribute);

                    $this->_enumAttach(
                        $getter,
                        function () use ($key) {
                            return $this->getEnumAttribute($key);
                        }
                    );

                    $this->_enumAttach(
                        $setter,
                        function ($value) use ($key, $enum) {
                            /** @var BaseEnum $value */
                            if ($value instanceof $enum) {
                                $this->setEnumAttribute($key, $value);
                            } elseif ($enum::isValidKey($value)) {
                                $this->setEnumAttribute($key, $enum::createByKey($value));
                            } elseif ($enum::isValidValue($value)) {
                                $this->setEnumAttribute($key, $enum::createByValue($value));
                            } else {
                                throw new InvalidCallException("The Enum class '{$enum}' does not accept the '{$value}' value.");
                            }
                        }
                    );
                }
            }
        }

        /**
         * @param string   $name
         * @param \Closure $method
         */
        private function _enumAttach($name, $method)
        {
            $className = get_class($this);

            $binded = \Closure::bind($method, $this, $className);

            $this->_enumMethods[$name] = $binded;
        }

        /**
         * @param string $attribute
         *
         * @return BaseEnum
         */
        public function getEnumAttribute($attribute)
        {
            return ((isset($this->_enumAttributes[$attribute])) ? $this->_enumAttributes[$attribute] : null);
        }

        /**
         * @param string $attribute
         * @param mixed  $value
         */
        public function setEnumAttribute($attribute, $value)
        {
            if ($value instanceof BaseEnum) {
                $this->_enumAttributes[$attribute] = $value;

                $this->setAttribute($attribute, $value->__toString());
            }
        }
        #endregion

        #region Generic Methods
        /**
         * @param string $name
         *
         * @return string
         */
        private function _enumGetter($name)
        {
            $name = InflectorHelper::camel2id($name, '_');

            return 'get'.InflectorHelper::camelize($name);
        }

        /**
         * @param string $name
         *
         * @return string
         */
        private function _enumSetter($name)
        {
            $name = InflectorHelper::camel2id($name, '_');

            return 'set'.InflectorHelper::camelize($name);
        }
        #endregion

        #region Magic Methods
        /**
         * @param $name
         * @param $value
         */
        public function __set($name, $value)
        {
            $this->_enumBind();

            $setter = $this->_enumSetter($name);

            if (array_key_exists($setter, $this->_enumMethods)) {
                call_user_func($this->_enumMethods[$setter], $value);

                return;
            }

            if (is_callable(['parent', '__set'])) {
                /** @noinspection PhpUndefinedClassInspection */
                parent::__set($name, $value);
            }
        }

        /**
         * @param $name
         *
         * @return mixed|null
         */
        public function __get($name)
        {
            $this->_enumBind();

            $getter = $this->_enumGetter($name);

            if (array_key_exists($getter, $this->_enumMethods)) {
                return call_user_func($this->_enumMethods[$getter]);
            }

            /** @noinspection PhpUndefinedClassInspection */
            return (is_callable(['parent', '__get']) ? parent::__get($name) : null);
        }

        /**
         * @param string $name
         *
         * @return bool
         */
        public function __isset($name)
        {
            $this->_enumBind();

            $getter = $this->_enumGetter($name);

            if (array_key_exists($getter, $this->_enumMethods)) {
                return true;
            }

            /** @noinspection PhpUndefinedClassInspection */
            return (is_callable(['parent', '__isset']) ? parent::__isset($name) : false);
        }

        /**
         * @param string $name
         */
        public function __unset($name)
        {
            $this->_enumBind();

            $getter = $this->_enumGetter($name);

            if (array_key_exists($getter, $this->_enumMethods)) {
                throw new InvalidCallException('Unsetting ENUM property: '.get_class($this).'::'.$name);
            }

            if (is_callable(['parent', '__unset'])) {
                /** @noinspection PhpUndefinedClassInspection */
                parent::__unset($name);
            }
        }
        #endregion
    }
