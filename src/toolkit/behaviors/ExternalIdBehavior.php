<?php

    namespace yiitk\behaviors;

    use Yii;
    use yii\base\InvalidConfigException;
    use yii\behaviors\AttributeBehavior;
    use yii\db\BaseActiveRecord;
    use yii\validators\UniqueValidator;
    use yiitk\helpers\ExternalIdHelper;
    use function call_user_func;
    use function is_callable;

    /**
     * ExternalIdBehavior automatically fills the specified attribute with a hash.
     *
     * Note: This behavior relies on php-intl extension for transliteration. If it is not installed it
     * falls back to replacements defined in [[\yii\helpers\Inflector::$transliteration]].
     *
     * To use ExternalIdBehavior, insert the following code to your ActiveRecord class:
     *
     * ```php
     * use yiitk\behaviors\ExternalIdBehavior;
     *
     * public function behaviors()
     * {
     *     return [
     *         [
     *             'class' => ExternalIdBehavior::class,
     *             'attribute' => 'externalId',
     *         ],
     *     ];
     * }
     * ```
     *
     * By default, ExternalIdBehavior will fill the `hash` attribute with a value that can be used as a hash.
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
     *             'class' => ExternalIdBehavior::class
     *         ]
     *     ];
     * }
     * ```
     */
    class ExternalIdBehavior extends AttributeBehavior
    {
        /**
         * @var string|array|null the attribute or list of attributes whose value will be converted into a hash
         * or `null` meaning that the `$value` property will be used to generate a hash.
         */
        public string|array|null $attribute = null;

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
         * @var bool whether to ensure generated hash value to be unique among owner class records.
         * If enabled behavior will validate hash uniqueness automatically. If validation fails it will attempt
         * generating unique hash value from based one until success.
         */
        public bool $ensureUnique = true;

        /**
         * @var bool whether to skip hash generation if [[attribute]] is null or an empty string.
         * If true, the behaviour will not generate a new hash if [[attribute]] is null or an empty string.
         */
        public bool $skipOnEmpty = false;

        /**
         * @var array configuration for hash uniqueness validator. Parameter 'class' may be omitted - by default
         * [[UniqueValidator]] will be used.
         * @see UniqueValidator
         */
        public array $uniqueValidator = [];

        /**
         * @var array
         */
        public array $uniqueValidatorAdditionalColumns = [];

        /**
         * @var callable hash value generator. This should be a PHP callable with following signature:
         *
         * ```php
         * function ($model)
         * {
         *     // return hash
         * }
         * ```
         */
        public $hashGenerator;

        /**
         * @var callable hash unique value generator. It is used in case [[ensureUnique]] enabled and generated
         * hash is not unique. This should be a PHP callable with following signature:
         *
         * ```php
         * function ($baseHash, $iteration, $model)
         * {
         *     // return uniqueHash
         * }
         * ```
         *
         * If not set unique hash will be generated adding incrementing suffix to the base hash.
         */
        public $uniqueHashGenerator;

        #region Initialization
        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            if (empty($this->attributes)) {
                $this->attributes = [BaseActiveRecord::EVENT_BEFORE_VALIDATE => $this->attribute];
            }

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
            if (!$this->isNewIdNeeded()) {
                return $this->owner->{$this->attribute};
            }

            if ($this->attribute !== null) {
                $hash = $this->generateHash();
            } else {
                $hash = parent::getValue($event);
            }

            return $this->ensureUnique ? $this->makeUnique($hash) : $hash;
        }
        #endregion

        #region Hash Generation
        /**
         * This method is called by [[getValue]] to generate the hash using as its default behavior the [[\yiitk\helpers\HashableHelper::hash()]] helper.
         * You may override it to customize hash generation or using configured callback.
         *
         * @return string the result.
         */
        protected function generateHash(): string
        {
            if (is_callable($this->hashGenerator)) {
                return call_user_func($this->hashGenerator, $this->owner);
            }

            return ExternalIdHelper::generate();
        }

        /**
         * This method is called by [[getValue]] when [[ensureUnique]] is true to generate the unique hash.
         * Calls [[generateUniqueHash]] until generated hash is unique and returns it.
         *
         * @param string $hash basic hash value
         *
         * @return string unique hash
         *
         * @throws InvalidConfigException
         *@see   getValue
         *
         * @see   generateUniqueId
         */
        protected function makeUnique(string $hash): string
        {
            $uniqueHash = $hash;

            while (!$this->validateHash($uniqueHash)) {
                $uniqueHash = $this->generateUniqueId($hash);
            }

            return $uniqueHash;
        }

        /**
         * Generates hash using configured callback or generating a new hash.
         *
         * @param string $baseHash  base hash value
         *
         * @return string new hash value
         */
        protected function generateUniqueId(string $baseHash): string
        {
            if (is_callable($this->uniqueHashGenerator)) {
                return call_user_func($this->uniqueHashGenerator, $baseHash, $this->owner);
            }

            return $this->generateHash();
        }
        #endregion

        #region Validators
        /**
         * Checks if given hash value is unique.
         *
         * @param string $hash hash value
         *
         * @return bool whether hash is unique.
         *
         * @throws InvalidConfigException
         */
        protected function validateHash(string $hash): bool
        {
            $uniqueValidatorConfig = ['class' => UniqueValidator::class];

            if (!empty($this->uniqueValidatorAdditionalColumns)) {
                $uniqueValidatorConfig['targetAttribute'] = array_merge([$this->attribute], $this->uniqueValidatorAdditionalColumns);
            }

            /**
             * @var UniqueValidator  $validator
             * @var BaseActiveRecord $model
             */
            $validator = Yii::createObject(array_merge($uniqueValidatorConfig, $this->uniqueValidator));

            $model = clone $this->owner;

            $model->clearErrors();

            $model->{$this->attribute} = $hash;

            $validator->validateAttribute($model, $this->attribute);

            return !$model->hasErrors();
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
        protected function isNewIdNeeded(): bool
        {
            return empty($this->owner->{$this->attribute});
        }
        #endregion

    }
