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
                                    <button class="project-options-btn" data-project-id="<?= $project->id ?>"
                                        data-project-title="<?= Html::encode($project->title) ?>" title="Опции">
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
                            <button class="create-btn" type="button" disabled
                                title="Сначала выберите проект">Загрузить</button>
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
        <div id="selectUsersModal" class="modal" style="display: none;">
            <div class="modal-content" style="max-width: 600px;">
                <header class="modal-header">
                    <h2>Выбор пользователей</h2>
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
                // Основные элементы
                const projectModal = document.getElementById('createProjectModal');
                const usersModal = document.getElementById('selectUsersModal');
                const participants = new Set();
                const currentUserId = <?= Yii::$app->user->id ?>;

                // Добавляем текущего пользователя как владельца
                participants.add(currentUserId.toString());

                // Открытие модального окна выбора пользователей
                document.getElementById('addParticipantBtn').addEventListener('click', function () {
                    openUsersModal();
                });

                // Функция открытия модального окна пользователей
                function openUsersModal() {
                    usersModal.style.display = 'block';
                    loadUsersList();
                }

                // Функция закрытия модального окна пользователей
                function closeUsersModal() {
                    usersModal.style.display = 'none';
                }

                // Загрузка списка пользователей
                function loadUsersList() {
                    fetch('<?= \yii\helpers\Url::to(['user-project/get-users']) ?>')
                        .then(response => response.json())
                        .then(users => {
                            const usersList = document.getElementById('usersList');
                            usersList.innerHTML = '';

                            if (users.length === 0) {
                                usersList.innerHTML = '<p>Нет пользователей для добавления</p>';
                                return;
                            }

                            users.forEach(user => {
                                const isCurrentUser = user.id == currentUserId;
                                const isAlreadyAdded = participants.has(user.id.toString());

                                const userDiv = document.createElement('div');
                                userDiv.className = 'user-item';
                                userDiv.style.padding = '10px';
                                userDiv.style.borderBottom = '1px solid #eee';
                                userDiv.style.display = 'flex';
                                userDiv.style.justifyContent = 'space-between';
                                userDiv.style.alignItems = 'center';

                                userDiv.innerHTML = `
                        <div>
                            <strong>${user.username}</strong>
                            <span style="color: #666; margin-left: 10px;">${user.role}</span>
                        </div>
                        <div>
                            <input type="checkbox" 
                                   value="${user.id}" 
                                   ${isAlreadyAdded || isCurrentUser ? 'checked disabled' : ''}
                                   class="user-checkbox">
                        </div>
                    `;

                                usersList.appendChild(userDiv);
                            });
                        })
                        .catch(error => {
                            console.error('Ошибка загрузки пользователей:', error);
                            document.getElementById('usersList').innerHTML = '<p>Ошибка загрузки пользователей</p>';
                        });
                }

                // Закрытие модальных окон
                document.querySelectorAll('.close-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        this.closest('.modal').style.display = 'none';
                    });
                });

                document.getElementById('cancelSelectUsers').addEventListener('click', closeUsersModal);
                document.querySelector('#selectUsersModal .close-btn').addEventListener('click', closeUsersModal);
                document.getElementById('cancelSelectUsers').addEventListener('click', closeUsersModal);

                // Клик вне модального окна
                window.addEventListener('click', function (event) {
                    if (event.target.classList.contains('modal')) {
                        event.target.style.display = 'none';
                    }
                });

                // Подтверждение выбора пользователей
                document.getElementById('confirmSelectUsers').addEventListener('click', function () {
                    const checkboxes = document.querySelectorAll('.user-checkbox:checked:not(:disabled)');

                    checkboxes.forEach(checkbox => {
                        participants.add(checkbox.value);
                    });

                    updateParticipantsList();
                    closeUsersModal();
                });

                // Обновление списка участников
                function updateParticipantsList() {
                    const participantsList = document.getElementById('participantsList');
                    const participantsInput = document.getElementById('projectParticipants');

                    let participantsHtml = '';

                    // Отображаем всех участников из participants Set
                    Array.from(participants).forEach(userId => {
                        const isOwner = userId == currentUserId;
                        let username, role;

                        if (isOwner) {
                            username = '<?= Html::encode(Yii::$app->user->identity->username) ?>';
                            role = 'Владелец проекта';
                        } else if (window.projectParticipantsData && window.projectParticipantsData[userId]) {
                            username = window.projectParticipantsData[userId].username;
                            role = window.projectParticipantsData[userId].role;
                        } else {
                            username = 'Пользователь #' + userId;
                            role = 'Участник';
                        }

                        participantsHtml += `
            <div class="participant" data-user-id="${userId}">
                <span class="participant-name">${username}</span>
                <span class="participant-role">${role}</span>
                <span class="remove-participant" style="cursor: pointer; color: red; margin-left: 10px; ${isOwner ? 'display: none;' : ''}">×</span>
            </div>
        `;
                    });

                    participantsList.innerHTML = participantsHtml;
                    participantsInput.value = Array.from(participants).join(',');

                    // Добавляем обработчики удаления
                    addRemoveHandlers();
                }

                // Обработчики удаления участников
                function addRemoveHandlers() {
                    document.querySelectorAll('.remove-participant').forEach(btn => {
                        btn.addEventListener('click', function () {
                            const participantDiv = this.closest('.participant');
                            const userId = participantDiv.getAttribute('data-user-id');

                            if (userId != currentUserId) {
                                participants.delete(userId);
                                participantDiv.remove();
                                document.getElementById('projectParticipants').value = Array.from(participants).join(',');
                            }
                        });
                    });
                }

                // Поиск пользователей
                document.getElementById('userSearch').addEventListener('input', function (e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const userItems = document.querySelectorAll('.user-item');

                    userItems.forEach(item => {
                        const userName = item.querySelector('strong').textContent.toLowerCase();
                        if (userName.includes(searchTerm)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });

                // Загрузка участников существующего проекта
                function loadProjectParticipants(projectId) {
                    fetch('<?= \yii\helpers\Url::to(['user-project/get-project-participants']) ?>?projectId=' + projectId)
                        .then(response => response.json())
                        .then(participantsData => {
                            participants.clear();
                            // Добавляем текущего пользователя как владельца (если он есть в списке — ок, иначе добавим)
                            const currentUserIdStr = currentUserId.toString();
                            let isOwnerInList = false;

                            // Добавляем всех участников из ответа
                            participantsData.forEach(participant => {
                                participants.add(participant.user_id.toString());
                                if (participant.user_id == currentUserId) {
                                    isOwnerInList = true;
                                }
                            });

                            // Если текущий пользователь не в списке — добавим как владельца
                            if (!isOwnerInList) {
                                participants.add(currentUserIdStr);
                            }

                            // Сохраняем данные участников для отображения
                            window.projectParticipantsData = participantsData.reduce((acc, p) => {
                                acc[p.user_id] = { username: p.username, role: p.role };
                                return acc;
                            }, {});

                            updateParticipantsList();
                        })
                        .catch(error => {
                            console.error('Ошибка загрузки участников:', error);
                            alert('Не удалось загрузить участников проекта.');
                        });
                }

                // Обработчик кнопок с троеточием для редактирования проекта
                document.querySelectorAll('.project-options-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const projectId = this.getAttribute('data-project-id');
                        const projectTitle = this.getAttribute('data-project-title');

                        document.getElementById('projectIdInput').value = projectId;
                        document.getElementById('projectTitle').value = projectTitle;
                        document.getElementById('deleteProjectBtn').style.display = 'block';

                        // Загружаем участников проекта
                        loadProjectParticipants(projectId);

                        projectModal.style.display = 'block';
                    });
                });

                // Обработчик кнопки создания нового проекта
                document.querySelector('.add-project-btn').addEventListener('click', function () {
                    document.getElementById('projectIdInput').value = '';
                    document.getElementById('projectTitle').value = '';
                    document.getElementById('deleteProjectBtn').style.display = 'none';

                    participants.clear();
                    participants.add(currentUserId.toString());
                    window.projectParticipantsData = {}; // сбрасываем данные
                    updateParticipantsList();

                    projectModal.style.display = 'block';
                });

                // Сохранение проекта
                document.getElementById('saveProjectBtn').addEventListener('click', function () {
                    const formData = new FormData(document.getElementById('manageProjectForm'));

                    fetch('<?= \yii\helpers\Url::to(['materials/manage-project']) ?>', {
                        method: 'POST',
                        body: formData,
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Проект успешно сохранен!');
                                projectModal.style.display = 'none';
                                window.location.reload();
                            } else {
                                alert('Ошибка: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка:', error);
                            alert('Произошла ошибка при сохранении проекта.');
                        });
                });

                // Инициализация списка участников при загрузке страницы
                updateParticipantsList();
            });
        </script>

        <script>
            // Обработчик кнопки загрузки
            document.getElementById('uploadBtn')?.addEventListener('click', function () {
                document.getElementById('fileInput').click();
            });

            // Отправка формы автоматически при выборе файла
            document.getElementById('fileInput')?.addEventListener('change', function () {
                this.form.submit();
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Кнопка "Готово" - сохранение проекта
                document.getElementById('saveProjectBtn').addEventListener('click', function () {
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
                document.getElementById('deleteProjectBtn').addEventListener('click', function () {
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
                document.querySelector('.close-btn').addEventListener('click', function () {
                    document.getElementById('createProjectModal').style.display = 'none';
                });

                // Открытие модального окна для создания нового проекта
                document.querySelector('.add-project-btn').addEventListener('click', function () {
                    document.getElementById('createProjectModal').style.display = 'block';
                    document.getElementById('projectIdInput').value = '';
                    document.getElementById('projectTitle').value = '';
                    document.getElementById('deleteProjectBtn').style.display = 'none';
                });
            });

            // Добавление участников
            document.getElementById('addParticipantBtn').addEventListener('click', function () {
                openUsersModal();
            });

            // Функция открытия модального окна пользователей
            function openUsersModal() {
                const usersModal = document.getElementById('selectUsersModal');
                usersModal.style.display = 'block';
                loadUsersList();
            }

            // Функция закрытия модального окна пользователей
            function closeUsersModal() {
                const usersModal = document.getElementById('selectUsersModal');
                usersModal.style.display = 'none';
            }

            // Загрузка списка пользователей
            function loadUsersList() {
                fetch('<?= \yii\helpers\Url::to(['user-project/get-users']) ?>')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(users => {
                        const usersList = document.getElementById('usersList');
                        usersList.innerHTML = '';

                        if (!users || users.length === 0) {
                            usersList.innerHTML = '<p>Нет пользователей для добавления</p>';
                            return;
                        }

                        users.forEach(user => {
                            const isCurrentUser = user.id == <?= Yii::$app->user->id ?>;
                            const isAlreadyAdded = participants.has(user.id.toString());

                            const userDiv = document.createElement('div');
                            userDiv.className = 'user-item';
                            userDiv.style.padding = '10px';
                            userDiv.style.borderBottom = '1px solid #eee';
                            userDiv.style.display = 'flex';
                            userDiv.style.justifyContent = 'space-between';
                            userDiv.style.alignItems = 'center';

                            userDiv.innerHTML = `
                    <div>
                        <strong>${user.username}</strong>
                        <span style="color: #666; margin-left: 10px;">${user.role}</span>
                    </div>
                    <div>
                        <input type="checkbox" 
                               value="${user.id}" 
                               ${isAlreadyAdded || isCurrentUser ? 'checked disabled' : ''}
                               class="user-checkbox">
                    </div>
                `;

                            usersList.appendChild(userDiv);
                        });
                    })
                    .catch(error => {
                        console.error('Ошибка загрузки пользователей:', error);
                        document.getElementById('usersList').innerHTML = '<p>Ошибка загрузки пользователей</p>';
                    });
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const projectOptionsButtons = document.querySelectorAll('.project-options-btn');
                const modal = document.getElementById('createProjectModal');
                const projectIdInput = document.getElementById('projectIdInput');
                const projectTitleInput = document.getElementById('projectTitle');
                const deleteProjectBtn = document.getElementById('deleteProjectBtn');

                // Обработчик кнопок с троеточием
                projectOptionsButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const projectId = this.getAttribute('data-project-id');
                        const projectTitle = this.getAttribute('data-project-title');

                        projectIdInput.value = projectId;
                        projectTitleInput.value = projectTitle;
                        deleteProjectBtn.style.display = 'block';

                        modal.style.display = 'block';
                    });
                });

                // Закрытие модального окна
                document.querySelector('.close-btn').addEventListener('click', function () {
                    modal.style.display = 'none';
                });
            });
        </script>