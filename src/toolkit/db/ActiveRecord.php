<?php

    namespace yiitk\db;

    use yii\behaviors\SluggableBehavior;
    use yiitk\behaviors\DateTimeBehavior;
    use yiitk\enum\base\EnumTrait;
    use yiitk\web\FlashMessagesTrait;

    /**
     * Class ActiveRecord
     *
     * @package yiitk\db
     */
    class ActiveRecord extends \yii\db\ActiveRecord
    {
        use EnumTrait, FlashMessagesTrait;

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
