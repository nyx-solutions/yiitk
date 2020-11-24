<?php

    namespace yiitk\behaviors;

    use JsonException;
    use Yii;
    use yii\base\Behavior;
    use yii\db\ActiveRecord;
    use yiitk\base\Security;

    /**
     * @property ActiveRecord $owner
     *
     * @property-read \yii\base\Security|Security $security
     */
    class CryptBehavior extends Behavior
    {
        /**
         * @var array
         */
        public array $attributes = [];

        /**
         * @var string
         */
        public string $key = '';

        /**
         * @var bool
         */
        public bool $json = false;

        //region Initialization
        /**
         * @return void
         */
        protected function initialization(): void
        {
            foreach ($this->attributes as $attribute) {
                $this->owner->setAttribute($attribute, null);
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
                ActiveRecord::EVENT_INIT          => function () {$this->initialization();},
                ActiveRecord::EVENT_AFTER_FIND    => function () {$this->decode();},
                ActiveRecord::EVENT_BEFORE_INSERT => function () {$this->encode();},
                ActiveRecord::EVENT_BEFORE_UPDATE => function () {$this->encode();},
                ActiveRecord::EVENT_AFTER_INSERT  => function () {$this->decode();},
                ActiveRecord::EVENT_AFTER_UPDATE  => function () {$this->decode();},
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
                    $value = null;
                }

                if (!empty($value)) {
                    if ($this->json) {
                        try {
                            $value = json_encode($value, JSON_THROW_ON_ERROR);
                        } catch (JsonException $e) {
                            $value = null;
                        }
                    }

                    if (!is_null($value)) {
                        $value = utf8_encode($this->security->encryptByKey((string)$value, $this->key));
                    }
                }

                $this->owner->setAttribute($attribute, $value);
            }
        }

        /**
         * @return void
         */
        protected function decode(): void
        {
            foreach ($this->attributes as $attribute) {
                $value = $this->owner->getAttribute($attribute);

                if (!empty($value)) {
                    $value = $this->security->decryptByKey(utf8_decode($value), $this->key);

                    if ($this->json) {
                        try {
                            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                        } catch (JsonException $e) {
                            $value = null;
                        }
                    }
                }

                $this->owner->setAttribute($attribute, $value);
            }
        }
        //endregion

        //region Getters
        /**
         * @return \yii\base\Security|Security
         */
        protected function getSecurity()
        {
            $security = Yii::$app->getSecurity();

            if ($security instanceof Security) {
                $this->key = (string)$security->secretKey;
            }

            return $security;
        }
        //endregion
    }
