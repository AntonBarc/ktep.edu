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
    <!-- Верхняя панель -->
    <header class="header">
    <div class="header-left">
        <img src="<?= Url::to('@web/images/logo.png') ?>" alt="Logo" class="header-logo">
    </div>

    <div class="header-right">
        <a href="#" class="header-icon">
            <i class="fas fa-bell"></i>
            <span class="tooltip">Уведомления</span>
        </a>
        <a href="#" class="header-icon">
            <i class="fas fa-envelope"></i>
            <span class="tooltip">Сообщения</span>
        </a>
        <a href="#" class="header-icon">
            <i class="fas fa-user-circle"></i>
            <span class="tooltip">Профиль</span>
        </a>
    </div>
    </header>

    <!-- Боковая панель -->
    <aside class="sidebar">
        <a href="<?= Url::to(['site/index']) ?>" class="icon-link">
            <i class="fas fa-home"></i>
            <span class="tooltip">Главная</span>
        </a>
        <a href="<?= Url::to(['site/login']) ?>" class="icon-link">
            <i class="fas fa-calendar-alt"></i>
            <span class="tooltip">Календарь</span>
        </a>
        <a href="<?= Url::to(['site/error']) ?>" class="icon-link">
            <i class="fas fa-users"></i>
            <span class="tooltip">Пользователи</span>
        </a>
        <a href="#" class="icon-link">
            <i class="fas fa-chart-bar"></i>
            <span class="tooltip">Статистика</span>
        </a>
        <a href="#" class="icon-link">
            <i class="fas fa-file-alt"></i>
            <span class="tooltip">Документы</span>
        </a>
        <a href="#" class="icon-link">
            <i class="fas fa-trash-alt"></i>
            <span class="tooltip">Корзина</span>
        </a>
        <a href="#" class="icon-link">
            <i class="fas fa-cog"></i>
            <span class="tooltip">Настройки</span>
        </a>
    </aside>

<?= $content ?>


<?php $this->endBody() ?>
<script src="<?= Url::to('@web/js/sidebar.js') ?>"></script>
</body>
</html>
<?php $this->endPage() ?>