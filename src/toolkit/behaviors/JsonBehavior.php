<?php

    namespace yiitk\behaviors;

    use JsonException;
    use yii\base\Behavior;
    use yii\db\ActiveRecord;

    /**
     * @property ActiveRecord $owner
     */
    class JsonBehavior extends Behavior
    {
        /**
         * @var array
         */
        public array $attributes = [];

        /**
         * @var string|null
         */
        public ?string $emptyValue = null;

        /**
         * @var bool
         */
        public bool $encodeBeforeValidation = true;

        //region Initialization
        /**
         * @return void
         */
        protected function initialization(): void
        {
            foreach ($this->attributes as $attribute) {
                $this->owner->setAttribute($attribute, []);
            }
        }
        //endregion

        //region Events
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function events()
        {
            return [
                ActiveRecord::EVENT_INIT            => function () {$this->initialization();},
                ActiveRecord::EVENT_AFTER_FIND      => function () {$this->decode();},
                ActiveRecord::EVENT_BEFORE_INSERT   => function () {$this->encode();},
                ActiveRecord::EVENT_BEFORE_UPDATE   => function () {$this->encode();},
                ActiveRecord::EVENT_AFTER_INSERT    => function () {$this->decode();},
                ActiveRecord::EVENT_AFTER_UPDATE    => function () {$this->decode();},
                ActiveRecord::EVENT_BEFORE_VALIDATE => function () {
                    if ($this->encodeBeforeValidation) {
                        $this->encodeValidate();
                    }
                },
                ActiveRecord::EVENT_AFTER_VALIDATE  => function () {
                    if ($this->encodeBeforeValidation) {
                        $this->decode();
                    }
                },
            ];
        }
        //endregion

        //region Encode & Decode
        /**
         * @return void
         */
        protected function encode(): void
        {
            foreach ($this->attributes as $attribute) {
                $value = $this->owner->getAttribute($attribute);

                if (is_null($value) || empty($value)) {
                    $value = $this->emptyValue;
                }

                $value = static::jsonEncode($value);

                $this->owner->setAttribute($attribute, (string)$value ?: $this->emptyValue);
            }
        }

        /**
         * @return void
         */
        protected function decode(): void
        {
            foreach ($this->attributes as $attribute) {
                $value = $this->owner->getAttribute($attribute);

                if (is_string($value)) {
                    $value = static::jsonDecode($value);
                }

                $this->owner->setAttribute($attribute, $value);
            }
        }

        /**
         * @param $value
         *
         * @return string|null
         */
        public static function jsonEncode($value): ?string
        {
            if (is_array($value) || is_object($value)) {
                try {
                    $value = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                } catch (JsonException $e) {
                    $value = null;
                }
            } else {
                $value = null;
            }

            if ($value === false) {
                $value = null;
            }

            return $value;
        }

        /**
         * @param mixed $value
         *
         * @return array|object|null
         */
        public static function jsonDecode($value)
        {
            if (is_string($value)) {
                try {
                    $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    $value = null;
                }
            }

            if (is_array($value) || is_object($value)) {
                return $value;
            }

            return null;
        }

        /**
         * @return void
         */
        protected function encodeValidate(): void
        {
            $this->encode();
        }
        //endregion
    }
