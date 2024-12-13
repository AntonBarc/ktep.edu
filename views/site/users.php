<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $users app\models\User[] */

$this->title = 'Список пользователей';
?>
<div class="container">
    <main class="content">
        <div class="mat-container">
            <aside class="mat-sidebar">
                <h2>Пользователи</h2>
                <ul>
                    <li><a href="#">Все пользователи</a></li>
                    <li><a href="#">Администраторы</a></li>
                    <li><a href="#">Преподаватели</a></li>
                    <li><a href="#">Студенты</a></li>
                </ul>
            </aside>

            <main class="mat-main-content">
                <header style="display: flex; justify-content: space-between; align-items: center;">
                    <h1>Список пользователей</h1>
                </header>

                <table class="content-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя пользователя</th>
                            <th>Auth Key</th>
                            <th>Access Token</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= Html::encode($user->id) ?></td>
                                <td><?= Html::encode($user->username) ?></td>
                                <td><?= Html::encode($user->authKey) ?></td>
                                <td><?= Html::encode($user->accessToken) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </main>
</div>
