<?php

    /**
     * @noinspection PhpMissingFieldTypeInspection
     */

    namespace yiitk\bootstrap4;

    use yii\base\Model;

    /**
     * Class ActiveForm
     */
    class ActiveForm extends \yii\bootstrap4\ActiveForm
    {
        /**
         * @var string
         */
        public $fieldClass = ActiveField::class;

        /**
         * @param Model  $model
         * @param string $attribute
         * @param array  $options
         *
         * @return \yii\widgets\ActiveField|ActiveField
         */
        public function field($model, $attribute, $options = [])
        {
            return parent::field($model, $attribute, $options);
        }
    }
