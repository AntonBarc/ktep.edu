<?php

/** @var yii\web\View $this */

use yii\helpers\Url;

$this->title = 'Учебный центр КТЭП';
?>
<!-- Основной контент -->
<div class="container">
    <main class="content">
        <h3>Панель администратора</h3>
        <!-- Статистические карточки -->
        <section class="cards">
            <a href="<?= Url::to(['materials/index']) ?>" class="card">
                <img src="<?= Url::to('@web/images/cards/material.png') ?>" alt="Материалы" class="card-image-m">
                <div>
                    <div class="card-title">
                        <?= Yii::$app->db->createCommand('SELECT COUNT(*) FROM materials')->queryScalar() ?>
                    </div>
                    <div class="card-text">материалов</div>
                </div>
            </a>
            <a href="<?= Url::to(['users/index']) ?>" class="card">
                <img src="<?= Url::to('@web/images/cards/user.png') ?>" alt="Пользователи" class="card-image">
                <div>
                    <div class="card-title">
                        <?= Yii::$app->db->createCommand('SELECT COUNT(*) FROM users')->queryScalar() ?>
                    </div>
                    <div class="card-text">пользователей</div>
                </div>
            </a>
            <a href="<?= Url::to(['groups/index']) ?>" class="card">
                <img src="<?= Url::to('@web/images/cards/group.png') ?>" alt="Группы" class="card-image">
                <div>
                    <div class="card-title">
                        2
                    </div>
                    <div class="card-text">группы</div>
                </div>
            </a>
        </section>

        <!-- Остальной код -->
    </main>
</div>