<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;
use yii\web\Response;

class UsersController extends Controller
{
    public function actionIndex()
    {
        $users = User::find()->all();
        $totalUsers = User::find()->count();
        $model = new User();
        $model->scenario = 'create'; // Устанавливаем сценарий для создания

        return $this->render('index', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new User();
        $model->scenario = 'create';
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return ['success' => true, 'message' => 'Пользователь успешно создан'];
            } else {
                return ['success' => false, 'errors' => $model->errors];
            }
        } else {
            return ['success' => false, 'errors' => $model->errors];
        }
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