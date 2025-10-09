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
                    <h2>
                        <a href="<?= Yii::$app->urlManager->createUrl(['materials/index']) ?>"
                            style="text-decoration: none; color: inherit;">
                            Проекты
                        </a>
                    </h2>
                    <button class="add-project-btn" title="Добавить проект">+</button>
                </header>
                <ul>
                    <?php foreach ($projects as $project): ?>
                        <li class="project-item <?= $projectId == $project->id ? 'active' : '' ?>">
                            <div class="project-row">
                                <a href="<?= Yii::$app->urlManager->createUrl(['materials/index', 'projectId' => $project->id]) ?>"
                                    class="project-title <?= $projectId == $project->id ? 'active' : '' ?>">
                                    <?= Html::encode($project->title) ?>
                                </a>
                                <button class="project-options-btn" data-project-id="<?= $project->id ?>"
                                    data-project-title="<?= Html::encode($project->title) ?>" title="Опции">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <main class="mat-main-content">


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

                <header style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                    <div>
                        <h1><?= Html::encode($currentProject ? $currentProject->title : 'Выберите проект') ?></h1>

                        <?php if ($currentProject && !empty($projectParticipants)): ?>
                            <div style="display: flex; align-items: center; margin-top: 8px;">
                                <?php
                                $index = 0;
                                foreach ($projectParticipants as $item):
                                    if (!$item->user)
                                        continue;

                                    $name = $item->user->username;
                                    $initials = mb_substr($name, 0, 1, 'UTF-8');
                                    ?>
                                    <div class="participant-avatar" data-project-id="<?= $currentProject->id ?>"
                                        title="<?= Html::encode($name) ?>" style="
                    position: relative;
                    left: <?= $index * (-10) ?>px;
                    cursor: pointer;
                    margin-bottom: 20px;
                    z-index: <?= count($projectParticipants) - $index ?>;
                ">
                                        <?php if (!empty($item->user->avatar)): ?>
                                            <img src="<?= Yii::getAlias('@web') . '/' . $item->user->avatar ?>"
                                                alt="<?= Html::encode($name) ?>" style="
                            width: 32px; 
                            height: 32px; 
                            border-radius: 50%; 
                            object-fit: cover; 
                            border: 2px solid #ddd;
                            box-shadow: 0 0 0 2px white;
                        ">
                                        <?php else: ?>
                                            <div style="
                        width: 32px; 
                        height: 32px; 
                        border-radius: 50%; 
                        background: #007bff; 
                        color: white; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center; 
                        font-weight: bold;
                        border: 2px solid white;
                    ">
                                                <?= Html::encode(strtoupper($initials)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    $index++;
                                endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="button-container" style="flex-shrink: 0;">
                        <?php if ($currentProject): ?>
                            <!-- Форма для "Создать" -->
                            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                            <?= $form->field($model, 'file', ['template' => '{input}{error}'])->fileInput([
                                'style' => 'display: none;',
                                'id' => 'fileInputCreate',
                            ]) ?>
                            <?= Html::activeHiddenInput($model, 'project_id', ['value' => $projectId]) ?>
                            <button class="create-btn" type="button" id="createBtn">Создать</button>
                            <?php ActiveForm::end(); ?>
                        <?php else: ?>
                            <button class="create-btn" type="button" id="createBtn">Создать</button>
                        <?php endif; ?>

                        <?php if ($currentProject): ?>
                            <!-- Форма для "Загрузить" -->
                            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                            <?= $form->field($model, 'file', ['template' => '{input}{error}'])->fileInput([
                                'style' => 'display: none;',
                                'id' => 'fileInputUpload',
                            ]) ?>
                            <?= Html::activeHiddenInput($model, 'project_id', ['value' => $projectId]) ?>
                            <button class="upload-btn" type="button" id="uploadBtn">Загрузить</button>
                            <?php ActiveForm::end(); ?>
                        <?php else: ?>
                            <button class="upload-btn" type="button" disabled
                                title="Сначала выберите проект">Загрузить</button>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if ($currentProject): ?>
                    <div class="materials-list">
                        <!-- Заголовок таблицы -->
                        <div class="materials-header">
                            <div class="header-cell checkbox-col">
                                <input type="checkbox" id="select-all" class="select-all-checkbox">
                            </div>
                            <div class="header-cell name-col" data-sort="title">
                                <span>Название</span>
                                <span class="sort-arrow" data-field="title"></span>
                            </div>
                            <div class="header-cell type-col" data-sort="type">
                                <span>Тип</span>
                                <span class="sort-arrow" data-field="type"></span>
                            </div>
                            <div class="header-cell assignment-col" data-sort="assignment">
                                <span>Назначения</span>
                                <span class="sort-arrow" data-field="assignment"></span>
                            </div>
                            <div class="header-cell author-col" data-sort="author">
                                <span>Автор</span>
                                <span class="sort-arrow" data-field="author"></span>
                            </div>
                            <div class="header-cell date-col" data-sort="created_at">
                                <span>Добавлено</span>
                                <span class="sort-arrow" data-field="created_at"></span>
                            </div>
                            <div class="header-cell settings-col">
                                <i class="bi bi-gear"></i>
                            </div>
                        </div>

                        <!-- Тело таблицы -->
                        <div class="materials-body">
                            <?php foreach ($materials as $material): ?>
                                <div class="material-row" data-id="<?= $material->id ?>">
                                    <div class="row-cell checkbox-col">
                                        <input type="checkbox" class="material-checkbox" value="<?= $material->id ?>">
                                    </div>
                                    <div class="row-cell name-col">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <!-- Иконка -->
                                            <?php
                                            $icon = '';
                                            switch ($material->type) {
                                                case 'folder':
                                                    $icon = 'bi-folder';
                                                    break;
                                                case 'course':
                                                    $icon = 'bi-file-earmark-text';
                                                    break;
                                                case 'trajectory':
                                                    $icon = 'bi-journal-bookmark';
                                                    break;
                                                case 'longread':
                                                    $icon = 'bi-file-richtext';
                                                    break;
                                                case 'test':
                                                    $icon = 'bi-check-circle';
                                                    break;
                                                case 'assignment':
                                                    $icon = 'bi-pencil-square';
                                                    break;
                                                case 'link':
                                                    $icon = 'bi-link-45deg';
                                                    break;
                                                default:
                                                    $icon = 'bi-file-earmark';
                                            }
                                            ?>
                                            <i class="bi <?= $icon ?>" style="font-size: 20px; color: #6c757d;"></i>
                                            <span><?= Html::encode($material->title) ?></span>
                                        </div>
                                    </div>
                                    <div class="row-cell type-col">
                                        <?= \app\models\Material::typesList()[$material->type] ?? 'Неизвестно' ?>
                                    </div>
                                    <div class="row-cell assignment-col">
                                        <?php
                                        // Если материал назначается конкретным пользователям — покажем "Назначен"
                                        // Пока просто показываем "-", если нет назначений
                                        echo '-';
                                        ?>
                                    </div>
                                    <div class="row-cell author-col">
                                        <?= Html::encode($material->author ? $material->author->username : 'Неизвестно') ?>
                                    </div>
                                    <div class="row-cell date-col">
                                        <?= date('d M Y, H:i', strtotime($material->created_at)) ?>
                                    </div>
                                    <div class="row-cell settings-col">
                                        <button class="settings-btn" title="Настройки">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-project-selected">
                        <p>Пожалуйста, выберите проект из списка слева чтобы просмотреть материалы.</p>
                    </div>
                <?php endif; ?>
                <!-- Конфигурация для JavaScript -->
                <div id="js-config" style="display:none;" data-user-id="<?= Yii::$app->user->id ?>"
                    data-username="<?= Html::encode(Yii::$app->user->identity->username) ?>"
                    data-projects="<?= htmlspecialchars(json_encode(array_column($projects, 'title', 'id'))) ?>"
                    data-csrf-token="<?= Yii::$app->request->csrfToken ?>" data-base-url="<?= Yii::$app->homeUrl ?>"
                    data-users-url="<?= \yii\helpers\Url::to(['user-project/get-users']) ?>"
                    data-project-id="<?= (int) $projectId ?: '' ?>">
                </div>
            </main>
        </div>

        <!-- Модальное окно "Создать материал" -->
        <div id="createMaterialModal" class="modal">
            <div class="modal-content" style="width: 80%; max-width: 600px; padding: 20px;">
                <header class="modal-header" style="margin-bottom: 20px;">
                    <h2>Создать материал</h2>
                    <span class="close-btn">&times;</span>
                </header>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div class="material-type" data-type="folder"
                        style="cursor: pointer; padding: 15px; border: 1px solid #ddd; border-radius: 8px; display: flex; align-items: center; gap: 10px; background: #f9f9f9;">
                        <i class="bi bi-folder" style="font-size: 24px; color: #ffc107;"></i>
                        <span>Папка</span>
                    </div>
                    <div class="material-type" data-type="course"
                        style="cursor: pointer; padding: 15px; border: 1px solid #ddd; border-radius: 8px; display: flex; align-items: center; gap: 10px; background: #f9f9f9;">
                        <i class="bi bi-file-earmark-text" style="font-size: 24px; color: #0d6efd;"></i>
                        <span>Курс</span>
                    </div>
                    <div class="material-type" data-type="longread"
                        style="cursor: pointer; padding: 15px; border: 1px solid #ddd; border-radius: 8px; display: flex; align-items: center; gap: 10px; background: #f9f9f9;">
                        <i class="bi bi-file-richtext" style="font-size: 24px; color: #6c757d;"></i>
                        <span>Лонгрид</span>
                    </div>
                    <div class="material-type" data-type="test"
                        style="cursor: pointer; padding: 15px; border: 1px solid #ddd; border-radius: 8px; display: flex; align-items: center; gap: 10px; background: #f9f9f9;">
                        <i class="bi bi-check-circle" style="font-size: 24px; color: #28a745;"></i>
                        <span>Тест</span>
                    </div>
                </div>
            </div>
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
                        <button type="button" class="add-participant-btn" id="addParticipantBtn">
                            <span class="icon">+</span>
                            <span>Добавление участников</span>
                        </button>
                    </div>

                    <hr class="separator">

                    <!-- Список участников -->
                    <div class="participants-list" id="participantsList">
                        <div class="participant" data-user-id="<?= Yii::$app->user->id ?>">
                            <span
                                class="participant-name"><?= Html::encode(Yii::$app->user->identity->username) ?></span>
                            <span class="participant-role">Владелец проекта</span>
                            <span class="remove-participant" style="display: none;">×</span>
                        </div>
                        <!-- Участники будут добавляться здесь динамически -->
                    </div>

                    <!-- Скрытое поле для хранения ID участников -->
                    <input type="hidden" id="projectParticipants" name="participants" value="">

                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>

                <footer class="modal-footer">
                    <button type="button" class="delete-project-btn" id="deleteProjectBtn">Удалить проект</button>
                    <button type="button" class="done-btn" id="saveProjectBtn">Готово</button>
                </footer>
            </div>
        </div>

        <!-- Модальное окно выбора пользователей -->
        <div id="selectUsersModal" class="modal">
            <div class="modal-content" style="max-width: 600px;">
                <header class="modal-header">
                    <h2>Добавление участников</h2>
                    <span class="close-btn">&times;</span>
                </header>
                <div class="modal-body">
                    <div style="margin-bottom: 15px;">
                        <input type="text" id="userSearch" placeholder="Поиск пользователей..."
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div id="usersList" style="max-height: 300px; overflow-y: auto;">
                        <p>Загрузка пользователей...</p>
                    </div>
                </div>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelSelectUsers">Отмена</button>
                    <button type="button" class="btn btn-primary" id="confirmSelectUsers">Добавить выбранных</button>
                </footer>
            </div>
        </div>

        <!-- Модальное окно "Создать папку" -->
        <div id="createFolderModal" class="modal">
            <div class="modal-content" style="width: 90%; max-width: 500px; padding: 20px;">
                <header class="modal-header" style="margin-bottom: 20px;">
                    <h2>Создать папку</h2>
                    <span class="close-btn">&times;</span>
                </header>

                <div class="modal-body">
                    <label for="folderName" style="display: block; margin-bottom: 8px; font-weight: bold;">
                        Название папки
                    </label>
                    <input type="text" id="folderName" placeholder="Введите название папки"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                </div>

                <footer class="modal-footer"
                    style="display: flex; justify-content: flex-end; gap: 10px; padding-top: 0;">
                    <button type="button" class="btn btn-secondary" id="cancelFolderBtn"
                        style="padding: 8px 16px; border: 1px solid #ccc; border-radius: 4px; background: grey; cursor: pointer;">
                        Отмена
                    </button>
                    <button type="button" class="btn btn-primary" id="createFolderBtn"
                        style="padding: 8px 16px; border: none; border-radius: 4px; background: #007bff; color: white; cursor: pointer;">
                        Создать
                    </button>
                </footer>
            </div>
        </div>

        <?php
        $this->registerJsFile('@web/js/materials-index.js', [
            'depends' => [\yii\web\JqueryAsset::class]
        ]);
        ?>