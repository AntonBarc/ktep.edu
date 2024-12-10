<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $materials app\models\Material[] */

$this->title = 'Список материалов';
?>
<div class="container">
    <main class="content">
        <div class="mat-container"></div>
        <aside class="mat-sidebar">
            <h2>Учебные материалы</h2>
            <ul>
                <li><a href="#">Недавние</a></li>
                <li><a href="#">Избранное</a></li>
                <li><a href="#">Доступные мне</a></li>
                <li><a href="#">Библиотека курсов</a></li>
                <li><a href="#">Корзина</a></li>
            </ul>
            <div class="projects">
                <h3>Проекты</h3>
                <ul>
                    <li><a href="#">Новый проект (Антон Барчей)</a></li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="mat-main-content">
            <header>
                <h1>Новый проект (Антон Барчей)</h1>
                <button class="upload-btn">Загрузить</button>
                <button class="create-btn">Создать</button>
            </header>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Тип</th>
                        <th>Назначения</th>
                        <th>Автор</th>
                        <th>Добавлено</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Новый курс</td>
                        <td>Курс</td>
                        <td>-</td>
                        <td>Вы</td>
                        <td>21 окт. 2024 г., 20:21</td>
                    </tr>
                </tbody>
            </table>
        </main>
</div>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
<?= $form->field($model, 'file')->fileInput() ?>

<div class="form-group">
    <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>