<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $materials app\models\Material[] */

$this->title = 'Список материалов';
?>
<div class="container">
    <main class="content">
        <table>
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Файл</th>
                    <th>Дата добавления</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materials as $material): ?>
                    <tr>
                        <td><?= Html::encode($material->title) ?></td>
                        <td><a href="<?= Yii::getAlias('@web/' . $material->file_path) ?>" download>Скачать</a></td>
                        <td><?= date('d.m.Y', strtotime($material->created_at)) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
</div>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'file')->fileInput() ?>

<div class="form-group">
    <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>