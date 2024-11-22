<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();
echo $form->field($model, 'title');
echo $form->field($model, 'content')->textarea();
echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
ActiveForm::end();
