<?php

namespace app\controllers;

use Yii;
use app\models\Project;
use app\models\Material;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Deletes an existing Project model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $project = $this->findModel($id);
        
        // Начинаем транзакцию для обеспечения целостности данных
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Удаляем файлы материалов с сервера
            foreach ($project->materials as $material) {
                $filePath = Yii::getAlias('@webroot') . $material->file_path;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // Удаляем проект (материалы удалятся каскадно из БД)
            $project->delete();
            
            $transaction->commit();
            
            Yii::$app->session->setFlash('success', 'Проект и все связанные материалы успешно удалены.');
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Ошибка при удалении проекта: ' . $e->getMessage());
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемый проект не существует.');
    }
}