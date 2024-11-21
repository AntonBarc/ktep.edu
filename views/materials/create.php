<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Добавить материал';
?>

<div class="container">
    <main class="content"></main>

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'type')->dropDownList(['Документ' => 'Документ', 'Видео' => 'Видео', 'Ссылка' => 'Ссылка']) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </main>
</div>