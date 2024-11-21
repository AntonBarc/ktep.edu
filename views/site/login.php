<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="centered-form">
    <div class="centered-form-container">
        <div class="site-login">
            <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

            <!-- Вывод ошибки, если неверные данные -->
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger text-center">
                    <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "<div class=\"form-group\">{input}{error}</div>", // Убраны label
                    'inputOptions' => ['class' => 'form-control'], // Поля ввода
                    'errorOptions' => ['class' => 'invalid-feedback d-block'], // Сообщение об ошибке
                ],
            ]); ?>

            <!-- Поле логина (убрана надпись) -->
            <?= $form->field($model, 'username')->textInput([
                'autofocus' => true,
                'placeholder' => 'Логин'
            ])->label(false) ?>

            <!-- Поле пароля (убрана надпись) -->
            <?= $form->field($model, 'password')->passwordInput([
                'placeholder' => 'Пароль'
            ])->label(false) ?>

            <!-- Чекбокс "Запомнить меня" -->
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => "<div class=\"custom-control custom-checkbox mb-3\">{input} {label}</div>{error}",
                'class' => 'custom-control-input',
                'labelOptions' => ['class' => 'custom-control-label'],
            ])->label('Запомнить меня') ?>

            <!-- Кнопка входа -->
            <div class="form-group text-center">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
