<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $materials app\models\Material[] */

$this->title = 'Список материалов';
?>
<div class="container">
    <main class="content">
        <div class="mat-container">
            <aside class="mat-sidebar">
                <h2>Учебные материалы</h2>
                <ul>
                    <li><a href="#">Недавние</a></li>
                    <li><a href="#">Избранное</a></li>
                    <li><a href="#">Доступные мне</a></li>
                    <li><a href="#">Библиотека курсов</a></li>
                    <li><a href="#">Корзина</a></li>
                </ul>
            </aside>

            <main class="mat-main-content">
                <header>
                    <h1>Новый проект (Антон Барчей)</h1>
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'file')->fileInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                    <button class="create-btn">Создать</button>
                </header>
                <table class="content-table">
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
            </main>
        </div>
    </main>
</div>