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
        $materials = Material::find()->all(); // Получаем все материалы
        $model = new Material(); // Создаем новую модель для формы загрузки

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) {
                // Генерация пути и названия материала
                $filePath = 'uploads/' . $model->file->baseName . '.' . $model->file->extension;
                $model->title = $model->file->baseName; // Название = имя файла (без расширения)

                if ($model->file->saveAs($filePath)) {
                    $model->file_path = $filePath;
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->author_id = Yii::$app->user->id; // Установите автора, если используется авторизация
                    $model->save(false);
                    Yii::$app->session->setFlash('success', 'Файл успешно загружен.');
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при сохранении файла.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка валидации файла.');
            }

            return $this->redirect(['index']); // Обновляем страницу после загрузки
        }

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
}
