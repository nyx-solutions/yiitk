<?php

    namespace yiitk\db;

    use Throwable;
    use Yii;
    use yii\behaviors\SluggableBehavior;
    use yii\db\Expression;
    use yii\db\StaleObjectException;
    use yiitk\behaviors\DateTimeBehavior;
    use yiitk\behaviors\ExternalIdBehavior;
    use yiitk\behaviors\HashableBehavior;
    use yiitk\behaviors\LinkManyBehavior;
    use yiitk\enum\base\EnumTrait;
    use yiitk\helpers\ArrayHelper;
    use yiitk\helpers\NumberHelper;
    use yiitk\validators\BrazilianMoneyValidator;
    use yiitk\validators\PercentageValidator;
    use yiitk\web\FlashMessagesTrait;
    use function is_array;

    /**
     * ActiveRecord
     *
     * @noinspection ContractViolationInspection
     */
    class ActiveRecord extends \yii\db\ActiveRecord
    {
        use EnumTrait;
        use FlashMessagesTrait;
        use ManyToManyTrait;

        public const SCENARIO_INSERT = 'insert';
        public const SCENARIO_UPDATE = 'update';

        /**
         * @var string
         */
        protected string $slugAttribute = 'title';

        /**
         * @var bool
         */
        protected bool $slugEnsureUnique = true;

        /**
         * @var bool
         */
        protected bool $slugImmutable = true;

        /**
         * @var string
         */
        protected string $hashableAttribute = 'hash';

        /**
         * @var string
         */
        protected string $externalIdAttribute = 'externalId';

        /**
         * @var array
         */
        protected array $externalIdAdditionalColumns = [];

        /**
         * @var bool
         */
        protected bool $addErrorsToFlashMessages = false;

        //region Scenarios
        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function scenarios()
        {
            $scenarios = parent::scenarios();

            $scenarios[self::SCENARIO_INSERT] = $scenarios[self::SCENARIO_DEFAULT];
            $scenarios[self::SCENARIO_UPDATE] = $scenarios[self::SCENARIO_DEFAULT];

            return $scenarios;
        }
        //endregion

        //region Rulesets
        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function rules()
        {
            $filters = [];
            $rules   = [];

            $moneyAttributes      = $this->moneyAttributes();
            $percentageAttributes = $this->percentageAttributes();

            if (is_array($moneyAttributes) && !empty($moneyAttributes)) {
                $filters[] = [array_keys($moneyAttributes), 'filter', 'filter' => fn ($value) => NumberHelper::brazilianCurrencyToFloat($value)];

                foreach ($moneyAttributes as $k => $attrRules) {
                    $min      = ((isset($attrRules['min'])) ? (float)$attrRules['min'] : null);
                    $max      = ((isset($attrRules['max'])) ? (float)$attrRules['max'] : null);
                    $required = true;

                    if (isset($attrRules['required'])) {
                        $required = (bool)$attrRules['required'];
                    }

                    $rules[] = [$k, BrazilianMoneyValidator::class, 'min' => $min, 'max' => $max];

                    if ($required) {
                        $rules[] = [$k, 'required'];
                    }
                }
            }

            if (is_array($percentageAttributes) && !empty($percentageAttributes)) {
                $filters[] = [array_keys($percentageAttributes), 'filter', 'filter' => fn ($value) => NumberHelper::percentToFloat($value)];

                foreach ($percentageAttributes as $k => $attrRules) {
                    $min      = ((isset($attrRules['min'])) ? (float)$attrRules['min'] : null);
                    $max      = ((isset($attrRules['max'])) ? (float)$attrRules['max'] : 100);
                    $required = true;

                    if (isset($attrRules['required'])) {
                        $required = (bool)$attrRules['required'];
                    }

                    $rules[] = [$k, PercentageValidator::class, 'min' => $min, 'max' => $max];

                    if ($required) {
                        $rules[] = [$k, 'required'];
                    }
                }
            }

            if ($this->hasAttribute($this->externalIdAttribute)) {
                $targetAttributes = [$this->externalIdAttribute];

                if (!empty($this->externalIdAdditionalColumns)) {
                    $targetAttributes = array_merge($targetAttributes, $this->externalIdAdditionalColumns);
                }

                $rules[] = [$this->externalIdAttribute, 'unique', 'targetAttribute' => $targetAttributes, 'skipOnEmpty' => true];
            }

            return ArrayHelper::merge($filters, $rules);
        }
        //endregion

        //region Float Attributes
        /**
         * @param array|null $attributes
         *
         * @return array
         *
         * @noinspection ParameterDefaultValueIsNotNullInspection
         */
        protected function parseFloatAttributesRules(?array $attributes = []): array
        {
            $realRules = [];

            if (!is_array($attributes)) {
                $attributes = [];
            }

            foreach ($attributes as $k => $v) {
                if (is_array($v)) {
                    $realRules[$k] = $v;
                } else {
                    $realRules[$v] = [];
                }
            }

            return $realRules;
        }
        //region Money Attributes
        /**
         * @return array
         */
        final public function money(): array
        {
            return array_keys($this->moneyAttributes());
        }

        public function moneyAttributes(): array
        {
            return [];
        }
        //endregion

        //region Percentages Attributes
        /**
         * @return array
         */
        final public function percentage(): array
        {
            return array_keys($this->percentageAttributes());
        }

        /**
         * @return array
         */
        public function percentageAttributes(): array
        {
            return [];
        }
        //endregion
        //endregion

        //region Behaviors
        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function behaviors()
        {
            $behaviors = parent::behaviors();

            if ($this->hasAttribute('createdAt') && $this->hasAttribute('updatedAt')) {
                $behaviors['datetime'] = [
                    'class'      => DateTimeBehavior::class,
                    'attributes' => [
                        self::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                        self::EVENT_BEFORE_UPDATE => 'updatedAt'
                    ]
                ];
            }

            if ($this->hasAttribute('slug')) {
                $behaviors['sluggable'] = [
                    'class'         => SluggableBehavior::class,
                    'attribute'     => $this->slugAttribute,
                    'slugAttribute' => 'slug',
                    'ensureUnique'  => $this->slugEnsureUnique,
                    'immutable'     => $this->slugImmutable,
                ];
            }

            if ($this->hasAttribute($this->externalIdAttribute)) {
                $behaviors['sluggable'] = [
                    'class'                            => ExternalIdBehavior::class,
                    'attribute'                        => $this->externalIdAttribute,
                    'uniqueValidatorAdditionalColumns' => $this->externalIdAdditionalColumns,
                ];
            }

            if ($this->hasAttribute($this->hashableAttribute)) {
                $behaviors['hashable'] = [
                    'class'         => HashableBehavior::class,
                    'attribute'     => $this->hashableAttribute,
                    'ensureUnique'  => true
                ];
            }

            $linkManyAttributes = $this->linkManyToManyRelations();

            if (is_array($linkManyAttributes) && !empty($linkManyAttributes)) {
                foreach ($linkManyAttributes as $key => $value) {
                    $extraColumns = [];
                    $attribute    = $value;

                    if (is_array($value)) {
                        $attribute    = $key;
                        $extraColumns = $value;
                    }

                    $keyAttribute     = ucfirst($attribute);
                    $behaviorKey      = "link{$keyAttribute}Behavior";
                    $populateProperty = "{$attribute}Ids";

                    $behaviors[$behaviorKey] = [
                        'class'                      => LinkManyBehavior::class,
                        'relation'                   => $attribute,
                        'relationReferenceAttribute' => $populateProperty,
                        'extraColumns'               => ArrayHelper::merge($extraColumns, ['createdAt' => new Expression('NOW()'), 'updatedAt' => new Expression('NOW()')])
                    ];
                }
            }

            return $behaviors;
        }
        //endregion

        //region Events
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function afterValidate()
        {
            if ($this->enableFlashMessages && $this->addErrorsToFlashMessages) {
                foreach ($this->getErrors() as $error) {
                    foreach ($error as $message) {
                        $this->addErrorMessage($message);
                    }
                }
            }

            parent::afterValidate();
        }
        //endregion

        //region Delete
        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function delete()
        {
            if ($this->enableFlashMessages) {
                try {
                    if (parent::delete()) {
                        $this->addSuccessMessage(Yii::t('yiitk', 'The requested entry was successfully removed.'));

                        return true;
                    }

                    $this->addErrorMessage(Yii::t('yiitk', 'It was not possible to remove the requested entry.'));

                    return false;
                } catch (Throwable) {
                    $this->addErrorMessage(Yii::t('yiitk', 'It was not possible to remove the requested entry because it was attached to another entry in the system.'));

                    return false;
                }
            } else {
                return parent::delete();
            }
        }

        /**
         * @return bool
         *
         * @throws Throwable
         */
        public function softDelete(): bool
        {
            try {
                if (parent::delete()) {
                    return true;
                }

                return false;
            } catch (Throwable) {}

            return false;
        }

        /**
         * @return false|int
         *
         * @throws Throwable
         * @throws StaleObjectException
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function hardDelete()
        {
            return parent::delete();
        }
        //endregion

        //region Fields
        /**
         * @inheritdoc
         *
         * @noinspection PhpMissingReturnTypeInspection
         */
        public function fields()
        {
            return $this->parseFields(parent::fields());
        }
        //endregion
    }
