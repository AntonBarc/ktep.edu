<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="wrap">

        <?php if (!Yii::$app->user->isGuest): ?>
            <!-- Верхняя панель -->
            <header class="header">
                <div class="header-left">
                    <img src="<?= Url::to('@web/images/logo.png') ?>" alt="Logo" class="header-logo">
                </div>

                <div class="header-right">
                    <div class="profile-menu">
                        <div class="header-icon profile-icon">
                            <i class="fas fa-user-circle"></i>
                            <span class="tooltip">Профиль</span>
                        </div>
                        <div class="dropdown-menu">
                            <p class="username">Антон Барчей</p>
                            <a href="<?= Url::to(['site/materials/index']) ?>">Портал пользователя</a>
                            <a href="<?= Url::to(['site/profile']) ?>">Мой профиль</a>
                            <?= Html::a('Выйти', ['site/logout'], [
                                'data-method' => 'post',
                                'data-confirm' => 'Вы уверены, что хотите выйти?'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Боковая панель -->
            <aside class="sidebar">
                <a href="<?= Url::to(['site/index']) ?>" class="icon-link">
                    <i class="fas fa-home"></i>
                    <span class="tooltip">Главная</span>
                </a>
                <a href="<?= Url::to(['/materials/index']) ?>" class="icon-link">
                    <i class="fas fa-file-alt"></i>
                    <span class="tooltip">Учебные материалы</span>
                </a>
                <a href="<?= Url::to(['site/users']) ?>" class="icon-link">
                    <i class="fas fa-users"></i>
                    <span class="tooltip">Пользователи</span>
                </a>
                <a href="<?= Url::to(['site/reports']) ?>" class="icon-link">
                    <i class="fa-solid fa-list-check"></i>
                    <span class="tooltip">Отчеты</span>
                </a>
                <a href="<?= Url::to(['site/settings']) ?>" class="icon-link">
                    <i class="fas fa-cog"></i>
                    <span class="tooltip">Настройки</span>
                </a>
            </aside>
        <?php endif; ?>

        <?= $content ?>

    </div>

    <?php $this->endBody() ?>
    <script src="<?= Url::to('@web/js/sidebar.js') ?>"></script>
    <script src="<?= Url::to('@web/js/dropmenu.js') ?>"></script>

</body>

</html>
<?php $this->endPage() ?>