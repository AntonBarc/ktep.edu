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
                                    <div title="<?= Html::encode($name) ?>" style="
                    position: relative;
                    left: <?= $index * (-12) ?>px; /* ← сдвигаем каждую следующую влево */
                    cursor: pointer;
                    z-index: <?= count($projectParticipants) - $index ?>; /* чтобы первая была сверху */
                ">
                                        <?php if (!empty($item->user->avatar)): ?>
                                            <img src="<?= Yii::getAlias('@web') . '/' . $item->user->avatar ?>"
                                                alt="<?= Html::encode($name) ?>" style="
                            width: 32px; 
                            height: 32px; 
                            border-radius: 50%; 
                            object-fit: cover; 
                            border: 2px solid #ddd;
                            box-shadow: 0 0 0 2px white; /* белый бордюр для контраста */
                        ">
                                        <?php else: ?>
                                            <div style="
                        width: 32px; 
                        height: 32px; 
                        margin-bottom: 10px;
                        border-radius: 50%; 
                        background: #007bff; 
                        color: white; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center; 
                        font-weight: bold;
                        border: 2px solid white; /* белый бордюр */
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
                            <button class="create-btn" type="button" disabled
                                title="Сначала выберите проект">Создать</button>
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

        <!-- Скрытое поле для хранения ID участников -->
        <input type="hidden" id="projectParticipants" name="participants" value="">

        <!-- Скрытое поле для хранения ID участников -->
        <input type="hidden" id="projectParticipants" name="participants" value="">

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Глобальные переменные
                const currentUserId = <?= Yii::$app->user->id ?>;
                const participants = new Set();
                let allUsers = {}; // ← данные всех пользователей

                // Инициализация: добавляем текущего пользователя
                participants.add(currentUserId.toString());

                // === ВСЕ ФУНКЦИИ ===
                // Функции для анимированного открытия/закрытия
                function openModal(modalId) {
                    document.getElementById(modalId).classList.add('show');
                }

                function closeModal(modalId) {
                    document.getElementById(modalId).classList.remove('show');
                }

                function openUsersModal() {
                    loadUsersList();
                    openModal('selectUsersModal');
                }

                function closeUsersModal() {
                    closeModal('selectUsersModal');
                }

                function loadUsersList() {
                    return new Promise((resolve, reject) => {
                        fetch('<?= \yii\helpers\Url::to(['user-project/get-users']) ?>')
                            .then(response => response.json())
                            .then(users => {
                                allUsers = {};
                                users.forEach(user => {
                                    allUsers[user.id.toString()] = {
                                        username: user.username,
                                        role: user.role
                                    };
                                });

                                const usersList = document.getElementById('usersList');
                                usersList.innerHTML = users.length === 0
                                    ? '<p>Нет пользователей для добавления</p>'
                                    : users.map(user => {
                                        const isCurrentUser = user.id == currentUserId;
                                        const isAlreadyAdded = participants.has(user.id.toString());
                                        return `
                            <div class="user-item" style="padding:10px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                                <div>
                                    <strong>${user.username}</strong>
                                    <span style="color:#666; margin-left:10px;">${user.role}</span>
                                </div>
                                <div>
                                    <input type="checkbox" 
                                           value="${user.id}" 
                                           ${isAlreadyAdded || isCurrentUser ? 'checked disabled' : ''}
                                           class="user-checkbox">
                                </div>
                            </div>
                        `;
                                    }).join('');

                                resolve(); // ← сигнал, что всё загружено
                            })
                            .catch(error => {
                                console.error('Ошибка загрузки пользователей:', error);
                                document.getElementById('usersList').innerHTML = '<p>Ошибка загрузки пользователей</p>';
                                reject(error);
                            });
                    });
                }

                function updateParticipantsList() {
                    const participantsList = document.getElementById('participantsList');
                    const participantsInput = document.getElementById('projectParticipants');

                    let html = Array.from(participants).map(userId => {
                        const isOwner = userId == currentUserId.toString();
                        let username, role;

                        if (isOwner) {
                            username = '<?= Html::encode(Yii::$app->user->identity->username) ?>';
                            role = 'Владелец проекта';
                        } else if (allUsers[userId]) {
                            username = allUsers[userId].username;
                            role = allUsers[userId].role;
                        } else {
                            username = 'Пользователь #' + userId;
                            role = 'Участник';
                        }

                        return `
                <div class="participant" data-user-id="${userId}">
                    <span class="participant-name">${username}</span>
                    <span class="participant-role">${role}</span>
                    <span class="remove-participant" 
                          style="cursor:pointer; color:red; margin-left:10px; ${isOwner ? 'display:none;' : ''}"
                          title="Удалить участника">&times;</span>
                </div>
            `;
                    }).join('');

                    participantsList.innerHTML = html;
                    participantsInput.value = Array.from(participants).join(',');

                    // Обработчики удаления
                    document.querySelectorAll('.remove-participant').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const userId = this.closest('.participant').dataset.userId;
                            if (userId != currentUserId.toString()) {
                                participants.delete(userId);
                                updateParticipantsList();
                            }
                        });
                    });
                }

                function loadProjectParticipants(projectId) {
                    fetch('<?= \yii\helpers\Url::to(['user-project/get-project-participants']) ?>?projectId=' + projectId)
                        .then(response => response.json())
                        .then(data => {
                            participants.clear();
                            data.forEach(p => participants.add(p.user_id.toString()));
                            if (!participants.has(currentUserId.toString())) {
                                participants.add(currentUserId.toString());
                            }
                            updateParticipantsList();
                        })
                        .catch(error => {
                            console.error('Ошибка загрузки участников:', error);
                            alert('Не удалось загрузить участников проекта.');
                        });
                }

                function loadProjectParticipantsAsync(projectId) {
                    return new Promise((resolve, reject) => {
                        fetch('<?= \yii\helpers\Url::to(['user-project/get-project-participants']) ?>?projectId=' + projectId)
                            .then(response => response.json())
                            .then(data => {
                                participants.clear();
                                data.forEach(p => participants.add(p.user_id.toString()));
                                if (!participants.has(currentUserId.toString())) {
                                    participants.add(currentUserId.toString());
                                }
                                updateParticipantsList();
                                resolve();
                            })
                            .catch(error => {
                                console.error('Ошибка загрузки участников:', error);
                                alert('Не удалось загрузить участников проекта.');
                                reject(error);
                            });
                    });
                }

                // === ОБРАБОТЧИКИ СОБЫТИЙ ===

                // Кнопка "Добавить участников"
                document.getElementById('addParticipantBtn')?.addEventListener('click', openUsersModal);

                // Подтверждение выбора
                document.getElementById('confirmSelectUsers')?.addEventListener('click', function () {
                    document.querySelectorAll('.user-checkbox:checked:not(:disabled)').forEach(checkbox => {
                        participants.add(checkbox.value.toString());
                    });
                    updateParticipantsList();
                    closeUsersModal();
                });

                // Отмена выбора
                document.getElementById('cancelSelectUsers')?.addEventListener('click', closeUsersModal);

                // Закрытие модалок по крестику или клику вне
                // По крестику
                document.querySelectorAll('.close-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        closeModal(btn.closest('.modal').id);
                    });
                });

                // По клику вне окна
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) {
                            closeModal(modal.id);
                        }
                    });
                });

                // Поиск в списке пользователей
                document.getElementById('userSearch')?.addEventListener('input', function (e) {
                    const term = e.target.value.toLowerCase();
                    document.querySelectorAll('.user-item').forEach(item => {
                        const name = item.querySelector('strong').textContent.toLowerCase();
                        item.style.display = name.includes(term) ? 'flex' : 'none';
                    });
                });

                // Открытие модалки для редактирования проекта
                document.querySelectorAll('.project-options-btn').forEach(btn => {
                    btn.addEventListener('click', async function () {
                        const id = this.dataset.projectId;
                        const title = this.dataset.projectTitle;
                        document.getElementById('projectIdInput').value = id;
                        document.getElementById('projectTitle').value = title;
                        document.getElementById('deleteProjectBtn').style.display = 'block';

                        // Ждём загрузки участников
                        await loadProjectParticipantsAsync(id);
                        openModal('createProjectModal');
                    });
                });

                // Создание нового проекта
                document.querySelector('.add-project-btn')?.addEventListener('click', function () {
                    document.getElementById('projectIdInput').value = '';
                    document.getElementById('projectTitle').value = '';
                    document.getElementById('deleteProjectBtn').style.display = 'none';
                    participants.clear();
                    participants.add(currentUserId.toString());
                    updateParticipantsList();
                    openModal('createProjectModal'); // ← для нового проекта данные готовы сразу
                });

                // Сохранение проекта
                document.getElementById('saveProjectBtn')?.addEventListener('click', function () {
                    const form = document.getElementById('manageProjectForm');
                    const formData = new FormData(form);
                    fetch('<?= \yii\helpers\Url::to(['materials/manage-project']) ?>', {
                        method: 'POST',
                        body: formData,
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '<?= \yii\helpers\Url::to(['materials/index']) ?>?projectId=' + (data.projectId || document.getElementById('projectIdInput').value);
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка сохранения:', error);
                        });
                });

                // Удаление проекта
                document.getElementById('deleteProjectBtn')?.addEventListener('click', function () {
                    const id = document.getElementById('projectIdInput').value;
                    const title = document.getElementById('projectTitle').value;
                    if (!id) return alert('Проект не выбран.');
                    if (!confirm(`Удалить проект "${title}" и все материалы?`)) return;

                    fetch('<?= \yii\helpers\Url::to(['materials/delete-project']) ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                        },
                        body: 'id=' + id
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '<?= \yii\helpers\Url::to(['materials/index']) ?>';
                            } else {
                                alert('Ошибка: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка удаления:', error);
                            alert('Ошибка при удалении проекта.');
                        });
                });

                loadUsersList().then(() => {
                    // После этого можно безопасно загружать участников проекта (если projectId известен)
                    const urlParams = new URLSearchParams(window.location.search);
                    const projectId = urlParams.get('projectId');
                    if (projectId) {
                        loadProjectParticipants(projectId);
                    }
                    updateParticipantsList();
                });
            });

            // Обработчик для кнопки "Создать"
            document.getElementById('createBtn')?.addEventListener('click', function () {
                document.getElementById('fileInputCreate')?.click();
            });

            // Обработчик для кнопки "Загрузить"
            document.getElementById('uploadBtn')?.addEventListener('click', function () {
                document.getElementById('fileInputUpload')?.click();
            });

            // Автоматическая отправка формы при выборе файла
            document.getElementById('fileInputCreate')?.addEventListener('change', function () {
                this.form.submit();
            });

            document.getElementById('fileInputUpload')?.addEventListener('change', function () {
                this.form.submit();
            });
        </script>