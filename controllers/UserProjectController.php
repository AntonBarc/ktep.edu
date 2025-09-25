<?php

namespace app\controllers;

use Yii;
use app\models\ProjectUser;
use app\models\Project;
use app\models\User;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;

class UserProjectController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['get-project-participants', 'get-users'],
                        'allow' => true,
                        'roles' => ['@'], // Только авторизованные пользователи
                    ],
                ],
            ],
        ];
    }

    /**
     * Получить список пользователей для добавления в проект
     */
    public function actionGetUsers()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()
            ->select(['id', 'username', 'role'])
            ->where(['!=', 'id', Yii::$app->user->id]) // Исключаем текущего пользователя
            ->asArray()
            ->all();

        return $users;
    }

    /**
     * Получить участников проекта
     */
    public function actionGetProjectParticipants($projectId)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Получаем все записи из user_project для данного проекта
        $userProjectRecords = \app\models\UserProject::find()
            ->where(['project_id' => $projectId])
            ->with('user') // жадная загрузка, чтобы избежать N+1
            ->all();

        $result = [];
        foreach ($userProjectRecords as $record) {
            // Безопасное получение данных пользователя
            if ($record->user) {
                $username = $record->user->username;
                $role = $record->user->role ?? 'Участник';
            } else {
                // Если пользователь удалён, но запись осталась
                $username = 'Пользователь #' . $record->user_id;
                $role = 'Удалён';
            }

            $result[] = [
                'user_id' => $record->user_id,
                'username' => $username,
                'role' => $role,
            ];
        }

        return $result;
    }

    /**
     * Добавить пользователя к проекту
     */
    public function actionAddUser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $projectId = Yii::$app->request->post('project_id');
        $userId = Yii::$app->request->post('user_id');

        $project = Project::findOne($projectId);
        if (!$project) {
            return ['success' => false, 'message' => 'Проект не найден'];
        }

        // Проверка прав доступа (только админ или владелец проекта)
        if (!Yii::$app->user->identity->isAdmin() && !$project->isUserOwner(Yii::$app->user->id)) {
            return ['success' => false, 'message' => 'Нет прав для управления проектом'];
        }

        $user = User::findOne($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }

        if ($project->addUser($userId)) {
            return ['success' => true, 'message' => 'Пользователь успешно добавлен к проекту'];
        } else {
            return ['success' => false, 'message' => 'Ошибка при добавлении пользователя'];
        }
    }

    /**
     * Удалить пользователя из проекта
     */
    public function actionRemoveUser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $projectId = Yii::$app->request->post('project_id');
        $userId = Yii::$app->request->post('user_id');

        $project = Project::findOne($projectId);
        if (!$project) {
            return ['success' => false, 'message' => 'Проект не найден'];
        }

        // Проверка прав доступа (только админ или владелец проекта)
        if (!Yii::$app->user->identity->isAdmin() && !$project->isUserOwner(Yii::$app->user->id)) {
            return ['success' => false, 'message' => 'Нет прав для управления проектом'];
        }

        // Нельзя удалить владельца проекта
        $projectUser = ProjectUser::findOne(['project_id' => $projectId, 'user_id' => $userId]);
        if ($projectUser && $projectUser->role === ProjectUser::ROLE_OWNER) {
            return ['success' => false, 'message' => 'Нельзя удалить владельца проекта'];
        }

        if ($project->removeUser($userId)) {
            return ['success' => true, 'message' => 'Пользователь успешно удален из проекта'];
        } else {
            return ['success' => false, 'message' => 'Ошибка при удалении пользователя'];
        }
    }

    /**
     * Получить проекты пользователя
     */
    public function actionGetUserProjects($userId = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        $isAdmin = Yii::$app->user->identity->isAdmin();

        // Админы могут просматривать проекты любого пользователя
        if ($isAdmin || $userId == Yii::$app->user->id) {
            $projects = Project::getAccessibleProjects($userId, $isAdmin);

            $result = [];
            foreach ($projects as $project) {
                $result[] = [
                    'id' => $project->id,
                    'name' => $project->name,
                    'created_at' => $project->created_at,
                ];
            }

            return ['success' => true, 'projects' => $result];
        }

        return ['success' => false, 'message' => 'Нет доступа к проектам пользователя'];
    }
}