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

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $filePath = 'uploads/' . $model->file->baseName . '.' . $model->file->extension;
                if ($model->file->saveAs($filePath)) {
                    // Заполняем данные для сохранения в базе данных
                    $model->title = $model->file->baseName;
                    $model->file_path = $filePath;
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->save(false);

                    // Возвращаем JSON-ответ об успешной загрузке
                    return $this->asJson(['success' => true, 'message' => 'Файл успешно загружен']);
                }
            }
        }

        return $this->asJson(['success' => false, 'message' => 'Ошибка при загрузке файла']);
    }


    public function actionFetchTableData()
{
    $materials = Material::find()->all();
    return $this->renderPartial('_table_rows', ['materials' => $materials]);
}

}
