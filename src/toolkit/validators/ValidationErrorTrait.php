<?php

    namespace yiitk\validators;

    /**
     * Trait ValidationErrorTrait
     *
     * @package common\components\validators
     */
    trait ValidationErrorTrait
    {
        /**
         * @param array $errors
         *
         * @return array
         */
        protected function validationErrors($errors)
        {
            if (!is_array($errors)) {
                $errors = [];
            }

            \Yii::$app->response->statusCode = 422;

            return $errors;
        }
    }
