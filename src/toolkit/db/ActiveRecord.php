<?php

    namespace yiitk\db;

    use yii\behaviors\SluggableBehavior;
    use yii\db\Expression;
    use yiitk\behaviors\DateTimeBehavior;
    use yiitk\behaviors\LinkManyBehavior;
    use yiitk\enum\base\EnumTrait;
    use yiitk\helpers\ArrayHelper;
    use yiitk\helpers\NumberHelper;
    use yiitk\validators\BrazilianMoneyValidator;
    use yiitk\validators\PercentageValidator;
    use yiitk\web\FlashMessagesTrait;

    /**
     * Class ActiveRecord
     *
     * @package yiitk\db
     */
    class ActiveRecord extends \yii\db\ActiveRecord
    {
        use EnumTrait, FlashMessagesTrait, ManyToManyTrait;

        const SCENARIO_INSERT = 'insert';
        const SCENARIO_UPDATE = 'update';

        /**
         * @var bool
         */
        protected $enableFlashMessages = true;

        /**
         * @var bool
         */
        protected $isSearch = false;

        /**
         * @var string
         */
        protected $slugAttribute = 'title';

        /**
         * @var bool
         */
        protected $slugEnsureUnique = true;

        /**
         * @var bool
         */
        protected $slugImmutable = true;

        #region Scenarios
        /**
         * @inheritdoc
         */
        public function scenarios()
        {
            $scenarios = parent::scenarios();

            $scenarios[self::SCENARIO_INSERT] = $scenarios[self::SCENARIO_DEFAULT];
            $scenarios[self::SCENARIO_UPDATE] = $scenarios[self::SCENARIO_DEFAULT];

            return $scenarios;
        }
        #endregion

        #region Rulesets
        /**
         * @inheritdoc
         */
        public function rules()
        {
            $filters = [];
            $rules   = [];

            $moneyAttributes      = $this->moneyAttributes();
            $percentageAttributes = $this->percentageAttributes();

            if (is_array($moneyAttributes) && count($moneyAttributes) > 0) {
                $filters[] = [array_keys($moneyAttributes), 'filter', 'filter' => fn ($value) => NumberHelper::brazilianCurrencyToFloat($value)];

                foreach ($moneyAttributes as $k => $attrRules) {
                    $min = ((isset($attrRules['min'])) ? (float)$attrRules['min'] : null);
                    $max = ((isset($attrRules['max'])) ? (float)$attrRules['max'] : null);

                    $rules[] = [$k, BrazilianMoneyValidator::class, 'min' => $min, 'max' => $max];
                }
            }

            if (is_array($percentageAttributes) && count($percentageAttributes) > 0) {
                $filters[] = [array_keys($percentageAttributes), 'filter', 'filter' => fn ($value) => NumberHelper::percentToFloat($value)];

                foreach ($percentageAttributes as $k => $attrRules) {
                    $min = ((isset($attrRules['min'])) ? (float)$attrRules['min'] : null);
                    $max = ((isset($attrRules['max'])) ? (float)$attrRules['max'] : null);

                    $rules[] = [$k, PercentageValidator::class, 'min' => $min, 'max' => $max];
                }
            }

            return ArrayHelper::merge($filters, $rules);
        }
        #endregion

        #region Money Attributes
        /**
         * @return array
         */
        final public function money()
        {
            return array_keys($this->moneyAttributes());
        }

        public function moneyAttributes()
        {
            return [];
        }
        #endregion

        #region Percentages Attributes
        /**
         * @return array
         */
        final public function percentage()
        {
            return array_keys($this->percentageAttributes());
        }

        /**
         * @return array
         */
        public function percentageAttributes()
        {
            return [];
        }
        #endregion

        #region Behaviors
        /**
         * @inheritdoc
         */
        public function behaviors()
        {
            $behaviors = parent::behaviors();

            if ($this->hasAttribute('createdAt') && $this->hasAttribute('updatedAt')) {
                $behaviors['datetime'] = [
                    'class'      => DateTimeBehavior::class,
                    'attributes' => [
                        ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                        ActiveRecord::EVENT_BEFORE_UPDATE => 'updatedAt'
                    ]
                ];
            }

            if ($this->hasAttribute('slug')) {
                $behaviors['sluggable'] = ['class' => SluggableBehavior::class, 'attribute' => $this->slugAttribute, 'slugAttribute' => 'slug', 'ensureUnique'  => $this->slugEnsureUnique, 'immutable' => $this->slugImmutable];
            }

            $linkManyAttributes = $this->linkManyToManyRelations();

            if (is_array($linkManyAttributes) && count($linkManyAttributes) > 0) {
                for ($i = 0; $i < count($linkManyAttributes); $i++) {
                    $attribute        = $linkManyAttributes[$i];
                    $keyAttribute     = ucfirst($attribute);
                    $key              = "link{$keyAttribute}Behavior";
                    $populateProperty = "{$attribute}Ids";

                    $behaviors[$key] = ['class' => LinkManyBehavior::class, 'relation' => $attribute, 'relationReferenceAttribute' => $populateProperty, 'extraColumns' => ['createdAt' => new Expression('NOW()'), 'updatedAt' => new Expression('NOW()')]];
                }
            }

            return $behaviors;
        }
        #endregion

        #region Events
        /**
         * @inheritdoc
         */
        public function afterValidate()
        {
            if ($this->enableFlashMessages) {
                $errors = $this->getErrors();

                foreach ($errors as $error) {
                    foreach ($error as $message) {
                        $this->addErrorMessage($message);
                    }
                }
            }

            parent::afterValidate();
        }
        #endregion

        #region Delete
        /**
         * {@inheritdoc}
         */
        public function delete()
        {
            if ($this->enableFlashMessages) {
                try {
                    if (parent::delete()) {
                        $this->addSuccessMessage(\Yii::t('yiitk', 'The requested entry was successfully removed.'));

                        return true;
                    } else {
                        $this->addErrorMessage(\Yii::t('yiitk', 'It was not possible to remove the requested entry.'));

                        return false;
                    }
                } catch (\Exception $e) {
                    $this->addErrorMessage(\Yii::t('yiitk', 'It was not possible to remove the requested entry because it was attached to another entry in the system.'));

                    return false;
                }
            } else {
                return parent::delete();
            }
        }

        /**
         * @return bool
         *
         * @throws \Throwable
         */
        public function softDelete()
        {
            try {
                if (parent::delete()) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        /**
         * @return false|int
         *
         * @throws \Throwable
         * @throws \yii\db\StaleObjectException
         */
        public function hardDelete()
        {
            return parent::delete();
        }
        #endregion

        #region Fields
        /**
         * @inheritdoc
         */
        public function fields()
        {
            return $this->parseFields(parent::fields());
        }
        #endregion
    }
