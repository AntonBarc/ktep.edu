<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Material */
/* @var $materials app\models\Material[] */

$this->title = 'Загрузка материалов';
?>
<div class="materials-upload">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="materials-form">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'file')->fileInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Загрузить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <h2>Список материалов</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Название</th>
                <th>Файл</th>
                <th>Дата добавления</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($materials) && is_array($materials)): ?>
                <?php foreach ($materials as $material): ?>
                    <tr>
                        <td><?= Html::encode($material->title) ?></td>
                        <td><a href="<?= Yii::getAlias('@web/' . $material->file_path) ?>" download>Скачать</a></td>
                        <td><?= date('d.m.Y', strtotime($material->created_at)) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Материалы отсутствуют</td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>
</div>