<?php

    /**
     * @var yii\web\View                        $this
     * @var yii\widgets\ActiveForm              $form
     * @var yiitk\gii\generators\enum\Generator $generator
     */

    echo $form->field($generator, 'enumClass');

    echo $form->field($generator, 'constants')->textarea();

    echo $form->field($generator, 'defaultConstant');

    echo $form->field($generator, 'languages');

    echo $form->field($generator, 'baseClass');
