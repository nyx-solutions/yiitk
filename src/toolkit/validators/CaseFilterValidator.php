<?php

    namespace yiitk\validators;

    /**
     * Class CaseFilterValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class CaseFilterValidator extends FilterValidator
    {
        /**
         * @var integer
         */
        public $mode = MB_CASE_UPPER;

        /**
         * @var bool
         */
        public $trim = false;

        /**
         * @inheritdoc
         */
        public function init()
        {
            $mode = $this->mode;
            $trim = $this->trim;

            $this->addFilter(
                function ($value) use ($mode, $trim) {
                    $value = (string)$value;

                    if ($trim) {
                        $value = trim($value);
                    }

                    if (!in_array($mode, [MB_CASE_LOWER, MB_CASE_LOWER, MB_CASE_TITLE])) {
                        $mode = MB_CASE_UPPER;
                    }

                    return mb_convert_case($value, $mode, \Yii::$app->charset);
                }
            );

            parent::init();
        }

        /**
         * @inheritdoc
         */
        public function validateAttribute($model, $attribute)
        {
            $value = (string)$model->$attribute;

            if ($this->trim) {
                $value = trim($value);
            }

            if (!in_array($this->mode, [MB_CASE_LOWER, MB_CASE_LOWER, MB_CASE_TITLE])) {
                $this->mode = MB_CASE_UPPER;
            }

            $model->$attribute = mb_convert_case($value, $this->mode, \Yii::$app->charset);
        }

        /**
         * @inheritdoc
         */
        public function clientValidateAttribute($model, $attribute, $view)
        {
            return null;
        }
    }
