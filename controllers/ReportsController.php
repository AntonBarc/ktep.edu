<?php

namespace app\controllers;

use yii\web\Controller;

class ReportsController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
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
