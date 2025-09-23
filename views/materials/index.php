<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $materials app\models\Material[] */
/* @var $model app\models\Material */
/* @var $projects app\models\Project[] */
/* @var $projectId int */

$this->title = 'Список материалов';
?>
<div class="container">
    <main class="content">
        <div class="mat-container">
            <aside class="mat-sidebar">
                <header style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Проекты</h2>
                    <button class="add-project-btn" title="Добавить проект">+</button>
                </header>
                <ul>
                    <?php foreach ($projects as $project): ?>
                        <li class="project-item <?= $projectId == $project->id ? 'active' : '' ?>">
                            <div class="project-container">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <a href="<?= Yii::$app->urlManager->createUrl(['materials/index', 'projectId' => $project->id]) ?>"
                                        class="<?= $projectId == $project->id ? 'active' : '' ?>">
                                        <?= Html::encode($project->title) ?>
                                    </a>
                                    <!-- Кнопка с троеточием -->
                                    <button class="project-options-btn"
                                        data-project-id="<?= $project->id ?>"
                                        data-project-title="<?= Html::encode($project->title) ?>"
                                        title="Опции">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <main class="mat-main-content">
                <header style="display: flex; justify-content: space-between; align-items: center;">

                    <?php
                    $currentProject = null;
                    if ($projectId) {
                        foreach ($projects as $project) {
                            if ($project->id == $projectId) {
                                $currentProject = $project;
                                break;
                            }
                        }
                    }
                    ?>

                    <h1><?= Html::encode($currentProject ? $currentProject->title : 'Выберите проект') ?></h1>

                    <div class="button-container">
                        <?php if ($currentProject): ?>
                            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                            <?= $form->field($model, 'file', [
                                'template' => '{input}{error}',
                            ])->fileInput([
                                'style' => 'display: none;',
                                'id' => 'fileInput',
                            ]) ?>
                            <?= Html::activeHiddenInput($model, 'project_id', ['value' => $projectId]) ?>
                            <button class="create-btn" type="button" id="createBtn">Cоздать</button>
                            <?php ActiveForm::end(); ?>
                        <?php else: ?>
                            <button class="create-btn" type="button" disabled title="Сначала выберите проект">Загрузить</button>
                        <?php endif; ?>
                        <?php if ($currentProject): ?>
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
                        <?php else: ?>
                            <button class="upload-btn" type="button" disabled title="Сначала выберите проект">Загрузить</button>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if ($currentProject): ?>
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
                <?php else: ?>
                    <div class="no-project-selected">
                        <p>Пожалуйста, выберите проект из списка слева чтобы просмотреть материалы.</p>
                    </div>
                <?php endif; ?>
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
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'action' => ['materials/manage-project'],
                        'id' => 'manageProjectForm',
                        'options' => ['enctype' => 'multipart/form-data']
                    ]); ?>
                    <?= Html::activeHiddenInput(new \app\models\Project(), 'id', ['id' => 'projectIdInput']) ?>
                    <label for="projectTitle">Название проекта</label>
                    <?= Html::activeTextInput(new \app\models\Project(), 'title', [
                        'id' => 'projectTitle',
                        'placeholder' => 'Название проекта'
                    ]) ?>

                    <!-- Участники проекта -->
                    <div class="project-participants">
                        <span>Участники проекта</span>
                        <button type="button" class="add-participant-btn">
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
                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>

                <footer class="modal-footer">
                    <button type="button" class="delete-project-btn" id="deleteProjectBtn">Удалить проект</button>
                    <button type="button" class="done-btn" id="saveProjectBtn">Готово</button>
                </footer>
            </div>
        </div>

        <script>
            // Обработчик кнопки загрузки
            document.getElementById('uploadBtn')?.addEventListener('click', function() {
                document.getElementById('fileInput').click();
            });

            // Отправка формы автоматически при выборе файла
            document.getElementById('fileInput')?.addEventListener('change', function() {
                this.form.submit();
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Кнопка "Готово" - сохранение проекта
                document.getElementById('saveProjectBtn').addEventListener('click', function() {
                    const form = document.getElementById('manageProjectForm');
                    const formData = new FormData(form);

                    fetch('<?= \yii\helpers\Url::to(['materials/manage-project']) ?>', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('createProjectModal').style.display = 'none';
                                window.location.href = '<?= \yii\helpers\Url::to(['materials/index']) ?>?projectId=' + data.projectId;
                            } else {
                                alert('Ошибка: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка:', error);
                            alert('Произошла ошибка при сохранении проекта.');
                        });
                });

                // Кнопка "Удалить проект"
                document.getElementById('deleteProjectBtn').addEventListener('click', function() {
                    const projectId = document.getElementById('projectIdInput').value;
                    const projectTitle = document.getElementById('projectTitle').value;

                    if (!projectId) {
                        alert('Невозможно удалить проект: проект не выбран.');
                        return;
                    }

                    if (confirm('Вы уверены, что хотите удалить проект "' + projectTitle + '" и ВСЕ материалы в нем? Это действие нельзя отменить.')) {
                        fetch('<?= \yii\helpers\Url::to(['materials/delete-project']) ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                                },
                                body: 'id=' + projectId
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Проект и все материалы успешно удалены!');
                                    window.location.href = '<?= \yii\helpers\Url::to(['materials/index']) ?>';
                                } else {
                                    alert('Ошибка: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Ошибка:', error);
                                alert('Произошла ошибка при удалении проекта.');
                            });
                    }
                });

                // Закрытие модального окна
                document.querySelector('.close-btn').addEventListener('click', function() {
                    document.getElementById('createProjectModal').style.display = 'none';
                });

                // Открытие модального окна для создания нового проекта
                document.querySelector('.add-project-btn').addEventListener('click', function() {
                    document.getElementById('createProjectModal').style.display = 'block';
                    document.getElementById('projectIdInput').value = '';
                    document.getElementById('projectTitle').value = '';
                    document.getElementById('deleteProjectBtn').style.display = 'none';
                });
            });

            // Добавление участников (заглушка)
            document.querySelector('.add-participant-btn').addEventListener('click', function() {
                alert('Добавление участников пока не реализовано.');
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const projectOptionsButtons = document.querySelectorAll('.project-options-btn');
                const modal = document.getElementById('createProjectModal');
                const projectIdInput = document.getElementById('projectIdInput');
                const projectTitleInput = document.getElementById('projectTitle');
                const deleteProjectBtn = document.getElementById('deleteProjectBtn');

                // Обработчик кнопок с троеточием
                projectOptionsButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const projectId = this.getAttribute('data-project-id');
                        const projectTitle = this.getAttribute('data-project-title');

                        projectIdInput.value = projectId;
                        projectTitleInput.value = projectTitle;
                        deleteProjectBtn.style.display = 'block';

                        modal.style.display = 'block';
                    });
                });

                // Закрытие модального окна
                document.querySelector('.close-btn').addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            });
        </script>