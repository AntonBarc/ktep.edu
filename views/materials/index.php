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
                    <button class="add-project-btn" title="Добавить проект" onclick="showCreateProjectModal()">+</button>
                </header>
                <ul>
                    <?php foreach ($projects as $project): ?>
                        <li>
                            <a href="<?= Yii::$app->urlManager->createUrl(['materials/index', 'projectId' => $project->id]) ?>"
                                class="<?= $projectId == $project->id ? 'active' : '' ?>">
                                <?= Html::encode($project->title) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <main class="mat-main-content">
                <header style="display: flex; justify-content: space-between; align-items: center;">

                    <?php
                    $currentProject = null;
                    foreach ($projects as $project) {
                        if ($project->id == $projectId) {
                            $currentProject = $project;
                            break;
                        }
                    }
                    ?>
                    <h1><?= Html::encode($currentProject ? $currentProject->title : 'Выберите проект') ?></h1>
                    <div class="button-container">
                        <button class="create-btn">Создать</button>
                        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                        <?= $form->field($model, 'file', [
                            'template' => '{input}{error}',
                        ])->fileInput([
                            'style' => 'display: none;',
                            'id' => 'fileInput',
                        ]) ?>
                        <?= Html::activeHiddenInput($model, 'project_id', ['value' => $projectId]) ?>
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

        <!-- Модальное окно -->
        <div id="createProjectModal" class="modal">
            <div class="modal-content">
                <header class="modal-header">
                    <h2>Управление проектом</h2>
                    <span class="close-btn">&times;</span>
                </header>

                <div class="modal-body">
                    <!-- Поле для ввода названия проекта -->
                    <label for="projectTitle">Название проекта</label>
                    <input type="text" id="projectTitle" name="projectTitle" placeholder="Новый проект (<?= Html::encode(Yii::$app->user->identity->username
                    ) ?>)">

                    <!-- Участники проекта -->
                    <div class="project-participants">
                        <span>Участники проекта</span>
                        <button class="add-participant-btn">
                            <span class="icon">+</span>
                            <span>Добавление участников</span>
                        </button>
                    </div>

                    <hr class="separator">

                    <!-- Список участников -->
                    <div class="participants-list">
                        <div class="participant">
                            <span class="participant-name"><?= Html::encode(Yii::$app->user->identity->username) ?></span>
                            <span class="participant-role">Владелец проекта</span>
                        </div>
                    </div>
                </div>

                <footer class="modal-footer">
                    <button class="delete-project-btn">Удалить проект</button>
                    <button class="done-btn">Готово</button>
                </footer>
            </div>
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

        <script>
            // Открытие модального окна
            document.querySelector('.add-project-btn').addEventListener('click', function() {
                document.getElementById('createProjectModal').style.display = 'block';
            });

            // Закрытие модального окна
            document.querySelector('.close-btn').addEventListener('click', function() {
                document.getElementById('createProjectModal').style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                const modal = document.getElementById('createProjectModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });

            // Добавление участников (заглушка)
            document.querySelector('.add-participant-btn').addEventListener('click', function() {
                alert('Добавление участников пока не реализовано.');
            });
        </script>