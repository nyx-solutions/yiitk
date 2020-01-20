<?php

    namespace yiitk\behaviors;

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
        public $attributes = [];

        /**
         * @var string
         */
        public $key = '';

        /**
         * @var bool
         */
        public $json = false;

        /**
         * @inheritdoc
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

        /**
         * @return void
         */
        protected function initialization(): void
        {
            foreach ($this->attributes as $attribute) {
                $this->owner->setAttribute($attribute, null);
            }
        }

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
                        $value = json_encode($value);
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
                        $value = json_decode($value, true);
                    }
                }

                $this->owner->setAttribute($attribute, $value);
            }
        }

        #region Getters
        /**
         * @return \yii\base\Security|Security
         */
        protected function getSecurity()
        {
            $security = \Yii::$app->getSecurity();

            if ($security instanceof Security) {
                $this->key = $security->secretKey;
            }

            return $security;
        }
        #endregion
    }
