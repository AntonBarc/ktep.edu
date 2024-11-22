<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Material;

class MaterialsController extends Controller
{
    public function actionIndex()
    {
        $materials = Material::find()->all();
        return $this->render('index', ['materials' => $materials]);
    }

    public function actionCreate()
{
    $model = new Material();

    if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
        // Загрузка файла
        $uploadedFile = UploadedFile::getInstance($model, 'file');
        if ($uploadedFile) {
            $filePath = 'uploads/' . $uploadedFile->baseName . '.' . $uploadedFile->extension;
            $uploadedFile->saveAs($filePath);
            $model->file_path = $filePath;  // Сохраняем путь к файлу в базе
        }
        // Сохранение материала
        if ($model->save()) {
            return $this->redirect(['index']);
        }
    }

    return $this->render('create', ['model' => $model]);
}



    public function actionUpload()
    {
        $model = new Material();

        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $filePath = 'uploads/' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs($filePath);
                $model->file_path = $filePath;
                $model->created_at = date('Y-m-d H:i:s');
                $model->save(false);
                return $this->redirect(['upload']);
            }
        }

        $materials = Material::find()->all();
        return $this->render('upload', ['model' => $model, 'materials' => $materials]);
    }
}
