<?php

    namespace yiitk\validators;

    /**
     * Class FilterValidator
     *
     * @package yiitk\validators
     */
    class FilterValidator extends \yii\validators\FilterValidator
    {
        #region Validations
        /**
         * @inheritdoc
         *
         * @noinspection ReturnTypeCanBeDeclaredInspection
         */
        public function clientValidateAttribute($model, $attribute, $view)
        {
            return null;
        }
        #endregion

        #region Helpers
        /**
         * @param callable $filter
         */
        public function addFilter(callable $filter): void
        {
            if (is_callable($filter)) {
                $this->filter = $filter;
            }
        }
        #endregion
    }
