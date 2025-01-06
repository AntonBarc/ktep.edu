<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $materials app\models\Material[] */
/* @var $model app\models\Material */

$this->title = 'Список материалов';
?>
<div class="container">
    <main class="content">
        <div class="mat-container">
            <aside class="mat-sidebar">
                <header style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Проекты</h2>
                    <button class="add-project-btn" title="Добавить проект">
                        <span>+</span>
                    </button>
                </header>
                <ul>
                    <li><a href="#">Новый проект 1</a></li>
                    <li><a href="#">Новый проект 2</a></li>
                    <li><a href="#">Новый проект 3</a></li>
                    <li><a href="#">Новый проект 4</a></li>
                    <li><a href="#">Новый проект 5</a></li>
                </ul>
            </aside>


            <main class="mat-main-content">
                <header style="display: flex; justify-content: space-between; align-items: center;">
                    <h1>Новый проект (Антон Барчей)</h1>
                    <div class="button-container">
                        <button class="create-btn">Создать</button>
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model, 'file', [
                            'template' => '{input}{error}', // Убираем метку и оставляем только поле
                        ])->fileInput([
                            'style' => 'display: none;', // Скрываем стандартный элемент "Обзор"
                            'id' => 'fileInput',
                        ]) ?>
                        <button class="upload-btn" type="button" id="uploadBtn">Загрузить</button>
                        <?php ActiveForm::end(); ?>
                    </div>
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

<script>
    // Обработчик кнопки загрузки
    document.getElementById('uploadBtn').addEventListener('click', function() {
        document.getElementById('fileInput').click(); // Открываем проводник для выбора файла
    });

    // Отправка формы автоматически при выборе файла
    document.getElementById('fileInput').addEventListener('change', function() {
        this.form.submit(); // Отправляем форму после выбора файла
    });
</script>