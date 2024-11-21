<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Material;

class MaterialsController extends Controller
{
    public function actionCreate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $name = Yii::$app->request->post('name');
        $type = Yii::$app->request->post('type');

        $model = new Material();
        $model->name = $name;
        $model->type = $type;
        $model->author_id = Yii::$app->user->id;
        $model->created_at = time();

        if ($model->save()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => $model->errors];
        }
    }

    public function actionUpload()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('file');
        if ($file) {
            $filePath = Yii::getAlias('@webroot/uploads/') . $file->name;

            if ($file->saveAs($filePath)) {
                return ['success' => true];
            }
        }

        return ['success' => false, 'message' => 'Ошибка при загрузке файла'];
    }
}
