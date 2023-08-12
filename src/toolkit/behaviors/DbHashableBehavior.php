<?php

    namespace yiitk\behaviors;

    use yii\base\InvalidConfigException;
    use yii\behaviors\AttributeBehavior;
    use yii\db\Expression;

    /**
     * HashableBehavior automatically fills the specified attribute with a hash.
     *
     * Note: This behavior relies on php-intl extension for transliteration. If it is not installed it
     * falls back to replacements defined in [[\yii\helpers\Inflector::$transliteration]].
     *
     * To use HashableBehavior, insert the following code to your ActiveRecord class:
     *
     * ```php
     * use yiitk\behaviors\HashableBehavior;
     *
     * public function behaviors()
     * {
     *     return [
     *         [
     *             'class' => HashableBehavior::class,
     *             'attribute' => 'title',
     *         ],
     *     ];
     * }
     * ```
     *
     * By default, HashableBehavior will fill the `hash` attribute with a value that can be used as a hash.
     *
     * Because attribute values will be set automatically by this behavior, they are usually not user input and should therefore
     * not be validated, i.e. the `hash` attribute should not appear in the [[\yii\base\Model::rules()|rules()]] method of the model.
     *
     * If your attribute name is different, you may configure the [[hashAttribute]] property like the following:
     *
     * ```php
     * public function behaviors()
     * {
     *     return [
     *         [
     *             'class' => HashableBehavior::class
     *         ]
     *     ];
     * }
     * ```
     */
    class DbHashableBehavior extends AttributeBehavior
    {
        /**
         * @var string|array|null the attribute or list of attributes whose value will be converted into a hash
         * or `null` meaning that the `$value` property will be used to generate a hash.
         */
        public $attribute;

        /**
         * @var callable|string|null the value that will be used as a hash. This can be an anonymous function
         * or an arbitrary value or null. If the former, the return value of the function will be used as a hash.
         * If `null` then the `$attribute` property will be used to generate a hash.
         * The signature of the function should be as follows,
         *
         * ```php
         * function ($event)
         * {
         *     // return hash
         * }
         * ```
         */
        public $value;

        /**
         * @var bool whether to skip hash generation if [[attribute]] is null or an empty string.
         * If true, the behaviour will not generate a new hash if [[attribute]] is null or an empty string.
         */
        public bool $skipOnEmpty = false;

        #region Initialization
        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            if ($this->attribute === null && $this->value === null) {
                throw new InvalidConfigException('Either "attribute" or "value" property must be specified.');
            }
        }
        #endregion

        #region Getters
        /**
         * @inheritdoc
         */
        protected function getValue($event)
        {
            if (!$this->isNewHashNeeded()) {
                return $this->owner->{$this->attribute};
            }

            if ($this->attribute !== null) {
                $hash = new Expression('DEFAULT');
            } else {
                $hash = parent::getValue($event);
            }

            return $hash;
        }
        #endregion

        #region Verifications
        /**
         * Checks whether the new hash generation is needed
         * This method is called by [[getValue]] to check whether the new hash generation is needed.
         * You may override it to customize checking.
         *
         * @return bool
         */
        protected function isNewHashNeeded(): bool
        {
            if (empty($this->owner->{$this->attribute})) {
                return true;
            }

            return false;
        }
        #endregion
    }
