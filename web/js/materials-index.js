document.addEventListener('DOMContentLoaded', function () {
    // === ЗАГРУЗКА КОНФИГУРАЦИИ ИЗ HTML ===
    const configEl = document.getElementById('js-config');
    if (!configEl) {
        console.error('Конфигурация не найдена!');
        return;
    }

    const config = {
        currentUserId: configEl.dataset.userId,
        currentUsername: configEl.dataset.username,
        projectTitles: JSON.parse(configEl.dataset.projects || '{}'),
        csrfToken: configEl.dataset.csrfToken,
        baseUrl: configEl.dataset.baseUrl
    };

    // === СОСТОЯНИЕ ===
    const state = {
        participants: new Set([config.currentUserId.toString()]),
        allUsers: {}
    };

    // === ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ===
    const utils = {
        openModal(modalId) {
            document.getElementById(modalId)?.classList.add('show');
        },
        closeModal(modalId) {
            document.getElementById(modalId)?.classList.remove('show');
        },
        showAlert(message) {
            alert(message);
        },
        // Генерация URL без PHP
        url(path) {
            return config.baseUrl + path.replace(/^\//, '');
        }
    };

    // === API ЗАПРОСЫ ===
    const api = {
        async loadUsers() {
            try {
                const configEl = document.getElementById('js-config');
                const response = await fetch(configEl.dataset.usersUrl);
                const users = await response.json();

                console.log('Получены пользователи:', users); // ← добавьте эту строку

                state.allUsers = users.reduce((acc, user) => {
                    acc[user.id.toString()] = { username: user.username, role: user.role };
                    return acc;
                }, {});

                console.log('state.allUsers:', state.allUsers); // ← и эту

                return users;
            } catch (error) {
                console.error('Ошибка загрузки пользователей:', error);
                throw error;
            }
        },

        async loadProjectParticipants(projectId) {
            try {
                const response = await fetch(utils.url(`user-project/get-project-participants?projectId=${projectId}`));
                if (!response.ok) throw new Error('Network error');
                const data = await response.json();

                state.participants.clear();
                data.forEach(p => state.participants.add(p.user_id.toString()));
                if (!state.participants.has(config.currentUserId.toString())) {
                    state.participants.add(config.currentUserId.toString());
                }

                return data;
            } catch (error) {
                console.error('Ошибка загрузки участников:', error);
                utils.showAlert('Не удалось загрузить участников проекта.');
                throw error;
            }
        }
    };

    // === UI ОБНОВЛЕНИЯ ===
    const ui = {
        updateParticipantsList() {
            const participantsList = document.getElementById('participantsList');
            const participantsInput = document.getElementById('projectParticipants');

            if (!participantsList || !participantsInput) return;

            const html = Array.from(state.participants).map(userId => {
                const isOwner = userId === config.currentUserId.toString();
                const user = state.allUsers[userId];
                const username = isOwner
                    ? config.currentUsername
                    : (user?.username || `Пользователь #${userId}`);
                const role = isOwner
                    ? 'Владелец проекта'
                    : (user?.role || 'Участник');

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
            participantsInput.value = Array.from(state.participants).join(',');

            // Обработчики удаления
            participantsList.querySelectorAll('.remove-participant').forEach(btn => {
                btn.addEventListener('click', function () {
                    const userId = this.closest('.participant').dataset.userId;
                    if (userId !== config.currentUserId.toString()) {
                        state.participants.delete(userId);
                        ui.updateParticipantsList();
                    }
                });
            });
        },

        renderUsersList(users) {
            const usersList = document.querySelector('#selectUsersModal #usersList');
            if (!usersList) {
                console.error('Элемент #usersList не найден!');
                return;
            }

            // Получаем массив [id, user] для сохранения ID
            const usersWithId = Object.entries(state.allUsers).map(([id, user]) => ({
                id: parseInt(id),
                username: user.username,
                role: user.role
            }));

            usersList.innerHTML = usersWithId.length === 0
                ? '<p>Нет пользователей для добавления</p>'
                : usersWithId.map(user => {
                    const isCurrentUser = user.id == config.currentUserId;
                    const isAlreadyAdded = state.participants.has(user.id.toString());
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
        }
    };

    // === ОБРАБОТЧИКИ СОБЫТИЙ ===
    const handlers = {
        init() {
            this.bindModalEvents();
            this.bindProjectEvents();
            this.bindUserEvents();
            this.bindMaterialEvents();
            this.bindFileEvents();
        },

        bindModalEvents() {
            document.querySelectorAll('.close-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const modal = btn.closest('.modal');
                    if (modal) utils.closeModal(modal.id);
                });
            });

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) utils.closeModal(modal.id);
                });
            });
        },

        bindProjectEvents() {
            document.querySelector('.add-project-btn')?.addEventListener('click', () => {
                document.getElementById('projectIdInput').value = '';
                document.getElementById('projectTitle').value = '';
                document.getElementById('deleteProjectBtn').style.display = 'none';
                state.participants.clear();
                state.participants.add(config.currentUserId.toString());
                ui.updateParticipantsList();
                utils.openModal('createProjectModal');
            });

            document.querySelectorAll('.project-options-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.dataset.projectId;
                    const title = btn.dataset.projectTitle;
                    document.getElementById('projectIdInput').value = id;
                    document.getElementById('projectTitle').value = title;
                    document.getElementById('deleteProjectBtn').style.display = 'block';

                    await api.loadProjectParticipants(id);
                    ui.updateParticipantsList();
                    utils.openModal('createProjectModal');
                });
            });

            document.querySelectorAll('.participant-avatar').forEach(avatar => {
                avatar.addEventListener('click', async () => {
                    const projectId = avatar.dataset.projectId;
                    if (!projectId) return;

                    document.getElementById('projectIdInput').value = projectId;
                    document.getElementById('projectTitle').value = config.projectTitles[projectId] || 'Проект';
                    document.getElementById('deleteProjectBtn').style.display = 'block';

                    await api.loadProjectParticipants(projectId);
                    ui.updateParticipantsList();
                    utils.openModal('createProjectModal');
                });
            });

            document.getElementById('saveProjectBtn')?.addEventListener('click', async () => {
                const form = document.getElementById('manageProjectForm');
                if (!form) return;

                const formData = new FormData(form);
                try {
                    const response = await fetch(utils.url('materials/manage-project'), {
                        method: 'POST',
                        body: formData,
                    });
                    const data = await response.json();

                    if (data.success) {
                        const projectId = data.projectId || document.getElementById('projectIdInput')?.value;
                        window.location.href = utils.url(`materials/index?projectId=${projectId}`);
                    }
                } catch (error) {
                    console.error('Ошибка сохранения:', error);
                }
            });

            document.getElementById('deleteProjectBtn')?.addEventListener('click', async () => {
                const id = document.getElementById('projectIdInput').value;
                const title = document.getElementById('projectTitle').value;
                if (!id) return utils.showAlert('Проект не выбран.');
                if (!confirm(`Удалить проект "${title}" и все материалы?`)) return;

                try {
                    const response = await fetch(utils.url('materials/delete-project'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': config.csrfToken
                        },
                        body: 'id=' + id
                    });
                    const data = await response.json();

                    if (data.success) {
                        window.location.href = utils.url('materials/index');
                    } else {
                        utils.showAlert('Ошибка: ' + data.message);
                    }
                } catch (error) {
                    console.error('Ошибка удаления:', error);
                    utils.showAlert('Ошибка при удалении проекта.');
                }
            });
        },

        bindUserEvents() {
            document.getElementById('addParticipantBtn')?.addEventListener('click', async () => {
                // Сначала открываем окно
                utils.openModal('selectUsersModal');

                // Ждём, пока DOM обновится (микрозадержка)
                await new Promise(resolve => setTimeout(resolve, 50));

                // Загружаем и отображаем пользователей
                await api.loadUsers();
                ui.renderUsersList(Object.values(state.allUsers));
            });

            document.getElementById('confirmSelectUsers')?.addEventListener('click', () => {
                document.querySelectorAll('.user-checkbox:checked:not(:disabled)').forEach(checkbox => {
                    state.participants.add(checkbox.value.toString());
                });
                ui.updateParticipantsList();
                utils.closeModal('selectUsersModal');
            });

            document.getElementById('cancelSelectUsers')?.addEventListener('click', () => {
                utils.closeModal('selectUsersModal');
            });

            document.getElementById('userSearch')?.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.user-item').forEach(item => {
                    const name = item.querySelector('strong')?.textContent.toLowerCase();
                    item.style.display = name?.includes(term) ? 'flex' : 'none';
                });
            });
        },

        bindMaterialEvents() {
            // Обработчик кнопки "Создать"
            document.getElementById('createBtn')?.addEventListener('click', (e) => {
                e.preventDefault();
                utils.openModal('createMaterialModal');
            });

            // Обработка выбора типа материала
            document.querySelectorAll('.material-type').forEach(item => {
                item.addEventListener('click', async () => {
                    const type = item.dataset.type;

                    // Если выбрана папка - открываем специальное окно
                    if (type === 'folder') {
                        utils.closeModal('createMaterialModal');
                        utils.openModal('createFolderModal');
                        document.getElementById('folderName').focus();
                        return;
                    }

                    // Для других типов - создание как обычно
                    const configEl = document.getElementById('js-config');
                    const projectId = parseInt(configEl.dataset.projectId) || null;

                    if (!projectId) {
                        utils.showAlert('Сначала выберите проект');
                        return;
                    }

                    try {
                        const response = await fetch(utils.url('materials/create-material'), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-Token': config.csrfToken
                            },
                            body: `project_id=${projectId}&type=${type}&title=Новый ${item.querySelector('span').textContent}`
                        });

                        const result = await response.json();
                        if (result.success) {
                            utils.closeModal('createMaterialModal');
                            window.location.reload();
                        } else {
                            utils.showAlert('Ошибка: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Ошибка создания материала:', error);
                        utils.showAlert('Не удалось создать материал');
                    }
                });
            });

            // Обработчики для окна создания папки
            document.getElementById('cancelFolderBtn')?.addEventListener('click', () => {
                utils.closeModal('createFolderModal');
                document.getElementById('folderName').value = '';
            });

            document.getElementById('createFolderBtn')?.addEventListener('click', async () => {
                const folderName = document.getElementById('folderName').value.trim();

                if (!folderName) {
                    utils.showAlert('Введите название папки');
                    return;
                }

                const configEl = document.getElementById('js-config');
                const projectId = parseInt(configEl.dataset.projectId) || null;

                if (!projectId) {
                    utils.showAlert('Сначала выберите проект');
                    return;
                }

                try {
                    const response = await fetch(utils.url('materials/create-material'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': config.csrfToken
                        },
                        body: `project_id=${projectId}&type=folder&title=${encodeURIComponent(folderName)}`
                    });

                    const result = await response.json();
                    if (result.success) {
                        utils.closeModal('createFolderModal');
                        document.getElementById('folderName').value = '';
                        window.location.reload();
                    } else {
                        utils.showAlert('Ошибка: ' + result.message);
                    }
                } catch (error) {
                    console.error('Ошибка создания папки:', error);
                    utils.showAlert('Не удалось создать папку');
                }
            });

            // Закрытие по крестику
            document.querySelector('#createFolderModal .close-btn')?.addEventListener('click', () => {
                utils.closeModal('createFolderModal');
                document.getElementById('folderName').value = '';
            });
        },

        bindFileEvents() {
            document.getElementById('uploadBtn')?.addEventListener('click', () => {
                document.getElementById('fileInputUpload')?.click();
            });

            ['fileInputCreate', 'fileInputUpload'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', function () {
                    this.form?.submit();
                });
            });
        }
    };
    // Внутри DOMContentLoaded

    // Сортировка
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const sortField = btn.dataset.sort;
            console.log('Сортировка по:', sortField);
            // Здесь можно реализовать сортировку на стороне сервера или клиента
            alert(`Сортировка по ${sortField} (реализуется позже)`);
        });
    });

    // Выбор всех элементов
    document.querySelector('.select-all-checkbox')?.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.material-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Выбор одного элемента
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('material-checkbox')) {
            const allCheckboxes = document.querySelectorAll('.material-checkbox');
            const selectAll = document.querySelector('.select-all-checkbox');
            selectAll.checked = Array.from(allCheckboxes).every(cb => cb.checked);
        }
    });
    // Внутри DOMContentLoaded

    // Состояние сортировки
    const sortState = {
        field: 'title', // поле по умолчанию
        direction: 'asc'    // направление по умолчанию
    };

    // Обновление отображения стрелок
    function updateSortArrows() {
        document.querySelectorAll('.sort-arrow').forEach(arrow => {
            const field = arrow.dataset.field;
            // Скрываем все стрелки
            arrow.classList.remove('active', 'desc');
            // Показываем активную стрелку
            if (field === sortState.field) {
                arrow.classList.add('active');
                if (sortState.direction === 'desc') {
                    arrow.classList.add('desc');
                }
            }
        });
    }

    // Обработчик клика по заголовку
    document.querySelectorAll('.materials-header .header-cell[data-sort]').forEach(cell => {
        cell.addEventListener('click', () => {
            const field = cell.dataset.sort;

            if (field === sortState.field) {
                // Переключаем направление
                sortState.direction = sortState.direction === 'asc' ? 'desc' : 'asc';
            } else {
                // Новое поле - сортировка по возрастанию
                sortState.field = field;
                sortState.direction = 'asc';
            }

            updateSortArrows();
            performSorting();
        });
    });

    // Функция сортировки (заглушка - реализуйте по своему усмотрению)
    function performSorting() {
        console.log('Сортировка:', sortState.field, sortState.direction);

        // Здесь можно:
        // 1. Отправить AJAX-запрос на сервер с параметрами сортировки
        // 2. Или отсортировать данные на клиенте (если их немного)

        // Пример AJAX-запроса:
        /*
        fetch(`${utils.url('materials/index')}?projectId=${config.projectId}&sort=${sortState.field}&order=${sortState.direction}`)
            .then(response => response.text())
            .then(html => {
                // Обновить только таблицу материалов
                document.querySelector('.materials-body').innerHTML = 
                    new DOMParser().parseFromString(html, 'text/html')
                        .querySelector('.materials-body').innerHTML;
            });
        */
    }

    // Инициализация при загрузке страницы
    updateSortArrows();

    // === ИНИЦИАЛИЗАЦИЯ ===
    async function init() {
        try {
            await api.loadUsers();
            const urlParams = new URLSearchParams(window.location.search);
            const projectId = urlParams.get('projectId');

            if (projectId) {
                await api.loadProjectParticipants(projectId);
            }

            ui.updateParticipantsList();
            handlers.init();
        } catch (error) {
            console.error('Ошибка инициализации:', error);
        }
    }

    init();
});