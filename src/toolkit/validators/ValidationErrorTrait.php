<?php

    namespace yiitk\validators;

    use Yii;

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
         *
         * @noinspection PhpUnused
         */
        protected function validationErrors(array $errors): array
        {
            Yii::$app->response->statusCode = 422;

            return $errors;
        }
    }
