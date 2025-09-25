<?php

namespace app\controllers;

use Yii;
use app\models\Material;
use app\models\Project;
use app\models\ProjectUser;
use app\models\User;
use yii\web\Controller;
use yii\web\UploadedFile;

class MaterialsController extends Controller
{
    public function actionIndex($projectId = null)
    {
        $isAdmin = Yii::$app->user->identity->isAdmin();
        
        // Получаем проекты в зависимости от прав пользователя
        $projects = Project::getAccessibleProjects(Yii::$app->user->id, $isAdmin);
        
        $materials = [];
        $model = new Material();
        
        if ($projectId) {
            $project = Project::findOne($projectId);
            if ($project && ($isAdmin || $project->isUserParticipant(Yii::$app->user->id))) {
                $materials = Material::find()->where(['project_id' => $projectId])->all();
            }
        }

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $model->file = UploadedFile::getInstance($model, 'file');
            
            if ($model->validate() && $model->file) {
                $uploadPath = Yii::getAlias('@webroot/uploads/');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $fileName = time() . '_' . $model->file->baseName . '.' . $model->file->extension;
                $filePath = 'uploads/' . $fileName;
                
                if ($model->file->saveAs($uploadPath . $fileName)) {
                    $model->file_path = $filePath;
                    $model->title = $model->file->baseName;
                    
                    if ($model->save(false)) {
                        return $this->redirect(['index', 'projectId' => $projectId]);
                    }
                }
            }
        }

        return $this->render('index', [
            'projects' => $projects,
            'projectId' => $projectId,
            'materials' => $materials,
            'model' => $model,
        ]);
    }

    /**
     * Управление проектом (создание/редактирование)
     */
public function actionManageProject()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $projectId = Yii::$app->request->post('Project')['id'] ?? null;
    $title = Yii::$app->request->post('Project')['title'] ?? ''; // Используем title
    $participants = Yii::$app->request->post('participants') ?? '';
    
    Yii::info('Начало сохранения проекта: ' . $title, 'project');
    
    try {
        if ($projectId) {
            $project = Project::findOne($projectId);
            if (!$project) {
                return ['success' => false, 'message' => 'Проект не найден'];
            }
        } else {
            $project = new Project();
        }
        
        // Используем правильное имя поля
        $project->title = $title; // или $project->name = $title; в зависимости от структуры
        
        if ($project->save()) {
            // Обрабатываем участников
            $participantIds = array_filter(explode(',', $participants));
            
            // Удаляем старых участников (кроме владельца) только при редактировании
            if ($projectId) {
                ProjectUser::deleteAll([
                    'and',
                    ['project_id' => $project->id],
                    ['!=', 'user_id', Yii::$app->user->id]
                ]);
            }
            
            // Добавляем новых участников
            foreach ($participantIds as $userId) {
                if ($userId != Yii::$app->user->id && !empty($userId)) {
                    $project->addUser($userId, ProjectUser::ROLE_PARTICIPANT);
                }
            }
            
            // Добавляем владельца, если его нет
            if (!$project->isUserParticipant(Yii::$app->user->id)) {
                $project->addUser(Yii::$app->user->id, ProjectUser::ROLE_OWNER);
            }
            
            return ['success' => true, 'projectId' => $project->id];
            
        } else {
            $errorMessage = 'Ошибка сохранения проекта';
            if ($project->hasErrors()) {
                $errors = $project->getFirstErrors();
                $errorMessage = implode(', ', $errors);
            }
            return ['success' => false, 'message' => $errorMessage];
        }
    } catch (\Exception $e) {
        Yii::error('Ошибка при сохранении проекта: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Ошибка при сохранении проекта: ' . $e->getMessage()];
    }
}

    /**
     * Удаление проекта
     */
    public function actionDeleteProject()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $projectId = Yii::$app->request->post('id');
        
        if (!$projectId) {
            return ['success' => false, 'message' => 'ID проекта не указан'];
        }
        
        $project = Project::findOne($projectId);
        
        if (!$project) {
            return ['success' => false, 'message' => 'Проект не найден'];
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Удаляем файлы материалов
            foreach ($project->materials as $material) {
                $filePath = Yii::getAlias('@webroot') . '/' . $material->file_path;
                if (file_exists($filePath) && is_file($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Удаляем проект (материалы и участники удалятся каскадно)
            if ($project->delete() === false) {
                throw new \Exception('Ошибка при удалении проекта из базы данных');
            }
            
            $transaction->commit();
            
            return ['success' => true, 'message' => 'Проект успешно удален'];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Ошибка удаления проекта: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Ошибка при удалении: ' . $e->getMessage()];
        }
    }
}