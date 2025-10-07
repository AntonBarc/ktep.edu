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
                const usersUrl = configEl.dataset.usersUrl;
                const response = await fetch(utils.url('user-project/get-users'));
                if (!response.ok) throw new Error('Network error');
                const users = await response.json();

                state.allUsers = users.reduce((acc, user) => {
                    acc[user.id.toString()] = { username: user.username, role: user.role };
                    return acc;
                }, {});

                return users;
            } catch (error) {
                console.error('Ошибка загрузки пользователей:', error);
                document.getElementById('usersList').innerHTML = '<p>Ошибка загрузки пользователей</p>';
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
            const usersList = document.getElementById('usersList');
            if (!usersList) return;

            usersList.innerHTML = users.length === 0
                ? '<p>Нет пользователей для добавления</p>'
                : users.map(user => {
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
                await api.loadUsers();
                ui.renderUsersList(Object.values(state.allUsers));
                utils.openModal('selectUsersModal');
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
            document.getElementById('createBtn')?.addEventListener('click', (e) => {
                e.preventDefault();
                utils.openModal('createMaterialModal');
            });

            document.querySelectorAll('.material-type').forEach(item => {
                item.addEventListener('click', () => {
                    const type = item.dataset.type;
                    console.log('Выбран тип:', type);
                    utils.closeModal('createMaterialModal');
                });
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