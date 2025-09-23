<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $users app\models\User[] */
/* @var $totalUsers int */
/* @var $model app\models\User */

$this->title = 'Список пользователей';
?>
<div class="container">
    <main class="content">
        <div class="mat-container">
            <aside class="mat-sidebar">
                <h2>Пользователи</h2>
                <ul>
                    <li><a href="<?= Url::to(['/users/index']) ?>">Пользователи</a></li>
                    <li><a href="<?= Url::to(['/users/groups']) ?>">Группы</a></li>
                    <li><a href="<?= Url::to(['/users/roles']) ?>">Роли</a></li>
                </ul>
            </aside>

            <main class="mat-main-content">
                <header style="display: flex; justify-content: space-between; align-items: center;">
                    <h1>Список пользователей</h1>

                    <!-- Кнопка Создать пользователя -->
                    <button type="button" class="create-user-btn" id="createUserBtn"
                        style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fas fa-user-plus"></i>
                        <span>Создать пользователя</span>
                    </button>
                </header>

                <!-- Счетчик пользователей -->
                <div style="margin-bottom: 20px;">
                    <strong>Всего пользователей: <?= $totalUsers ?></strong>
                </div>

                <table class="content-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя пользователя</th>
                            <th>Auth Key</th>
                            <th>Access Token</th>
                            <th>Роль</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= Html::encode($user->id) ?></td>
                                    <td><?= Html::encode($user->username) ?></td>
                                    <td><?= Html::encode($user->authKey) ?></td>
                                    <td><?= Html::encode($user->accessToken) ?></td>
                                    <td><?= Html::encode($user->role) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">Нет данных для отображения</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </main>
</div>

<!-- Модальное окно создания пользователя -->
<div id="createUserModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <header class="modal-header">
            <h2>Создание пользователя</h2>
            <span class="close-btn">&times;</span>
        </header>

        <div class="modal-body">
            <?php $form = ActiveForm::begin([
                'id' => 'create-user-form',
                'enableAjaxValidation' => true,
            ]); ?>

            <div style="margin-bottom: 15px;">
                <label for="username">Логин *</label>
                <?= $form->field($model, 'username', [
                    'template' => '{input}{error}',
                    'options' => ['class' => 'form-group']
                ])->textInput([
                    'id' => 'username',
                    'class' => 'form-control',
                    'placeholder' => 'Введите логин',
                    'required' => true
                ]) ?>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password">Пароль *</label>
                <?= $form->field($model, 'password', [
                    'template' => '{input}{error}',
                    'options' => ['class' => 'form-group']
                ])->passwordInput([
                    'id' => 'password',
                    'class' => 'form-control',
                    'placeholder' => 'Введите пароль',
                    'required' => true
                ]) ?>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="role">Роль *</label>
                <?= $form->field($model, 'role', [
                    'template' => '{input}{error}',
                    'options' => ['class' => 'form-group']
                ])->dropDownList([
                    'user' => 'User',
                    'admin' => 'Admin'
                ], [
                    'id' => 'role',
                    'class' => 'form-control',
                    'prompt' => 'Выберите роль',
                    'required' => true
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <footer class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-secondary" id="cancelCreateUser">Отмена</button>
            <button type="button" class="btn btn-primary" id="saveUserBtn">Создать</button>
        </footer>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('createUserModal');
        const createBtn = document.getElementById('createUserBtn');
        const closeBtn = document.querySelector('#createUserModal .close-btn');
        const cancelBtn = document.getElementById('cancelCreateUser');
        const saveBtn = document.getElementById('saveUserBtn');
        const form = document.getElementById('create-user-form');

        // Открытие модального окна
        createBtn.addEventListener('click', function() {
            modal.style.display = 'block';
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
            document.getElementById('role').value = '';
        });

        // Закрытие модального окна
        function closeModal() {
            modal.style.display = 'none';
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Клик вне модального окна
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        // Сохранение пользователя
        saveBtn.addEventListener('click', function() {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const role = document.getElementById('role').value;

            if (!username || !password || !role) {
                alert('Пожалуйста, заполните все обязательные поля');
                return;
            }

            if (password.length < 6) {
                alert('Пароль должен содержать минимум 6 символов');
                return;
            }

            const formData = new FormData();
            formData.append('User[username]', username);
            formData.append('User[password]', password);
            formData.append('User[role]', role);

            fetch('<?= Url::to(['/users/create']) ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Пользователь успешно создан!');
                        closeModal();
                        window.location.reload();
                    } else {
                        let errorMessage = 'Ошибка при создании пользователя';
                        if (data.errors) {
                            errorMessage += ': ' + Object.values(data.errors).flat().join(', ');
                        }
                        alert(errorMessage);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при создании пользователя');
                });
        });
    });
</script>

<style>
    .modal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 0;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 1.25rem;
    }

    .close-btn {
        font-size: 24px;
        cursor: pointer;
        color: #aaa;
    }

    .close-btn:hover {
        color: #000;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #eee;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
    }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
    }

    .form-group {
        margin-bottom: 0;
    }
</style>