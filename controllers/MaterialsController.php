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
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName('file');
        if ($file) {
            $path = Yii::getAlias('@webroot/uploads/' . $file->name);
            if ($file->saveAs($path)) {
                return json_encode(['success' => true]);
            }
        }
        return json_encode(['success' => false]);
    }
}
