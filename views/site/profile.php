<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

$this->title = 'Учебный центр КТЭП';
?>
<!-- Основной контент -->
<div class="container">
    <main class="content">
    <h1>Профиль</h1>
        <!-- Статистические карточки -->
        <section class="cards">
            <div class="card">
                <img src="<?= Url::to('@web/images/cards/material.png') ?>" alt="Материалы" class="card-image-m">
                <div>
                    <div class="card-title">15</div>
                    <div class="card-text">материалов</div>
                </div>
            </div>
            <div class="card">
                <img src="<?= Url::to('@web/images/cards/user.png') ?>" alt="Пользователи" class="card-image">
                <div>
                    <div class="card-title">346</div>
                    <div class="card-text">пользователей</div>
                </div>
            </div>
            <div class="card">
                <img src="<?= Url::to('@web/images/cards/group.png') ?>" alt="Группы" class="card-image">
                <div>
                    <div class="card-title">2</div>
                    <div class="card-text">группы</div>
                </div>
            </div>
        </section>

        <!-- Задачи и материалы -->
        <div class="task-section">
            <h4>Непроверенные задания</h4>
            <div class="tabs">
                <button class="active">Мои 0</button>
                <button>Все 0</button>
            </div>
            <div class="task-list">
                <p>Пока нет заданий на проверку</p>
            </div>
        </div>
    </main>
</div>
</div>