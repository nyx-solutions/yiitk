<?php


    namespace yiitk\validators;

    /**
     * Class FilterValidator
     *
     * @package yiitk\validators
     */
    class FilterValidator extends \yii\validators\FilterValidator
    {
        /**
         * @param callable $filter
         */
        public function addFilter($filter)
        {
            if (is_callable($filter)) {
                $this->filter = $filter;
            }
        }

        /**
         * @inheritdoc
         */
        public function clientValidateAttribute($model, $attribute, $view)
        {
            return null;
        }
    }
