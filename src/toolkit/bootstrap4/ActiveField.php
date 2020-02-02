<?php

    namespace yiitk\bootstrap4;

    use yiitk\db\ActiveRecord;
    use yiitk\helpers\ArrayHelper;
    use yiitk\helpers\NumberHelper;

    /**
     * Class ActiveField
     */
    class ActiveField extends \yii\bootstrap4\ActiveField
    {
        /**
         * @var ActiveRecord
         */
        public $model;

        /**
         * @var array
         */
        public $labelOptions = ['class' => 'control-label'];

        #region Initialization
        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            $this->labelOptions = ['class' => 'control-label'];
        }
        #endregion

        #region Fields
        /**
         * @inheritdoc
         */
        public function textInput($options = [])
        {
            if ($this->model instanceof ActiveRecord) {
                if (in_array($this->attribute, $this->model->money())) {
                    $options = $this->findBrazilianMoneyFieldOptions($options);
                } elseif (in_array($this->attribute, $this->model->percentage())) {
                    $options = $this->findPercentageFieldOptions($options);
                }
            }

            return parent::textInput($this->ensureFormControlClass($options));
        }

        /**
         * @inheritdoc
         */
        public function textarea($options = [])
        {
            return parent::textarea($this->ensureFormControlClass($options));
        }

        /**
         * @inheritdoc
         */
        public function dropdownList($items, $options = [])
        {
            return parent::dropdownList($items, $this->ensureFormControlClass($options));
        }
        #endregion

        #region Money and Percentage Fields
        /**
         * @param array  $additionalOptions
         *
         * @return array
         */
        protected function findPercentageFieldOptions($additionalOptions = [])
        {
            $dataOptions = [];

            if (isset($additionalOptions['data']) && is_array($additionalOptions['data'])) {
                $dataOptions = $additionalOptions['data'];

                unset($additionalOptions['data']);
            }

            $options = ['maxlength' => 7, 'class' => 'format-percentage form-control', 'data' => $this->formmatedDataRule($dataOptions, true)];

            return ArrayHelper::merge($additionalOptions, $options);
        }

        /**
         * @param array $additionalOptions
         *
         * @return array
         */
        protected function findBrazilianMoneyFieldOptions($additionalOptions = [])
        {
            $dataOptions = [];

            if (isset($additionalOptions['data']) && is_array($additionalOptions['data'])) {
                $dataOptions = $additionalOptions['data'];

                unset($additionalOptions['data']);
            }

            $options = ['class' => 'format-money form-control', 'data' => $this->formmatedDataRule($dataOptions, false)];

            return ArrayHelper::merge($additionalOptions, $options);
        }

        /**
         * @param array $rules
         * @param bool  $percentage
         *
         * @return array
         */
        protected function formmatedDataRule($rules = [], $percentage = false)
        {
            if (!is_array($rules)) {
                $rules = [];
            }

            if ($this->model->hasProperty($this->attribute)) {
                $attribute = $this->attribute;

                $rules['formatted'] = (($percentage) ? NumberHelper::toPercentText($this->model->$attribute) : NumberHelper::toBrazilianCurrency($this->model->$attribute));
            } else {
                $rules['formatted'] = (($percentage) ? NumberHelper::toPercentText(0) : NumberHelper::toBrazilianCurrency(0));
            }

            return $rules;
        }
        #endregion

        #region Helpers
        /**
         * @param array $options
         *
         * @return array
         */
        protected function ensureFormControlClass($options = [])
        {
            $fcClass = 'form-control';

            if (!is_array($options)) {
                $options = [];
            }

            if (!isset($options['class']) || empty($options['class'])) {
                $options['class'] = "{$fcClass}";

                return $options;
            }

            $classes = explode(' ', $options['class']);

            if (!in_array($fcClass, $classes)) {
                $classes[] = $fcClass;
            }

            $options['class'] = implode(' ', $classes);

            return $options;
        }
        #endregion
    }
