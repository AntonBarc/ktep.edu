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
        $model = new Material(); // Создание модели для формы загрузки файла
        return $this->render('index', [
            'materials' => $materials,
            'model' => $model,
        ]);
    }


    public function actionCreate()
    {
        $model = new Material();  // Создание новой модели
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Логика сохранения материала
        }
        return $this->render('create', ['model' => $model]);  // Передача модели в представление
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
