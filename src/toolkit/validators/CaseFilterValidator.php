<?php

    namespace yiitk\validators;

    use Yii;

    /**
     * Class CaseFilterValidator
     *
     * @category Validator
     * @author   Jonatas Sas
     */
    class CaseFilterValidator extends FilterValidator
    {
        /**
         * @var int
         */
        public $mode = MB_CASE_UPPER;

        /**
         * @var bool
         */
        public bool $trim = false;

        #region Initialization
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function init()
        {
            $mode = $this->mode;
            $trim = $this->trim;

            $this->addFilter(
                static function ($value) use ($mode, $trim) {
                    $value = (string)$value;

                    if ($trim) {
                        $value = trim($value);
                    }

                    if (!in_array($mode, [MB_CASE_LOWER, MB_CASE_LOWER, MB_CASE_TITLE], true)) {
                        $mode = MB_CASE_UPPER;
                    }

                    return mb_convert_case($value, $mode, Yii::$app->charset);
                }
            );

            parent::init();
        }
        #endregion

        #region Validations
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function validateAttribute($model, $attribute)
        {
            $value = (string)$model->$attribute;

            if ($this->trim) {
                $value = trim($value);
            }

            if (!in_array((int)$this->mode, [MB_CASE_LOWER, MB_CASE_LOWER, MB_CASE_TITLE], true)) {
                $this->mode = MB_CASE_UPPER;
            }

            $model->$attribute = mb_convert_case($value, $this->mode, Yii::$app->charset);
        }

        /**
         * @inheritdoc
         *
         * @noinspection SenselessMethodDuplicationInspection
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function clientValidateAttribute($model, $attribute, $view)
        {
            return null;
        }
        #endregion
    }
