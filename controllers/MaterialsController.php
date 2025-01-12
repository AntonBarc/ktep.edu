<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Material;
use app\models\Project;

class MaterialsController extends Controller
{
    public function actionIndex($projectId = null)
    {
        $projects = Project::find()->all(); // Получаем список проектов
        $model = new Material(); // Модель для загрузки материалов

        // Если проект выбран, фильтруем материалы по projectId
        $materials = $projectId
            ? Material::find()->where(['project_id' => $projectId])->all()
            : [];

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()) {
                $filePath = 'uploads/' . $model->file->baseName . '.' . $model->file->extension;
                $model->title = $model->file->baseName; // Название файла
                $model->file_path = $filePath;
                $model->project_id = $projectId; // Связь с проектом
                $model->created_at = date('Y-m-d H:i:s');
                $model->author_id = Yii::$app->user->id; // Автор (если авторизация используется)

                if ($model->file->saveAs($filePath) && $model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Файл успешно загружен.');
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка при сохранении файла.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка валидации файла.');
            }

            return $this->redirect(['index', 'projectId' => $projectId]); // Обновляем страницу
        }

        return $this->render('index', [
            'projects' => $projects,
            'projectId' => $projectId,
            'materials' => $materials,
            'model' => $model,
            'currentProject' => $projectId ? Project::findOne($projectId) : null,
        ]);
    }

    public function actionManageProject()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $postData = Yii::$app->request->post('Project');
        $projectId = $postData['id'] ?? null;

        $project = $projectId ? Project::findOne($projectId) : new Project();

        if (!$project) {
            return ['success' => false, 'message' => 'Проект не найден.'];
        }

        $project->title = $postData['title'];

        if ($project->save()) {
            return ['success' => true, 'projectId' => $project->id];
        }

        return ['success' => false, 'message' => 'Ошибка при сохранении проекта.'];
    }




    public function actionCreateProject()
    {
        $project = new Project();

        if ($project->load(Yii::$app->request->post()) && $project->save()) {
            Yii::$app->session->setFlash('success', 'Проект успешно создан.');
            return $this->redirect(['index']);
        }

        return $this->render('create-project', ['model' => $project]);
    }

    public function actionDeleteProject()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $projectId = Yii::$app->request->post('id');
        $project = Project::findOne($projectId);

        if ($project && $project->delete()) {
            return ['success' => true];
        }

        return ['success' => false];
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
