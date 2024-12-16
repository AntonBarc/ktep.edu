<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\User;

class UsersController extends Controller
{
    public function actionIndex()
    {
        $users = User::find()->all();

        // Передаем данные в представление
        return $this->render('index', [
            'users' => $users,
        ]);
    }

    public function actionGroups()
    {
        return $this->render('groups'); 
    }

    public function actionRoles()
    {
        return $this->render('roles'); 
    }
}
