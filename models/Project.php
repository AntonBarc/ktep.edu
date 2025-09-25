<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property string $title
 * @property string $created_at
 * 
 * @property Material[] $materials
 * @property ProjectUser[] $projectUsers
 * @property User[] $users
 */
class Project extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название проекта',
            'created_at' => 'Дата создания',
        ];
    }

    public function getName()
    {
        return $this->title;
    }

    // Сеттер для обратной совместимости
    public function setName($value)
    {
        $this->title = $value;
    }


    /**
     * Связь с материалами
     */
    public function getMaterials()
    {
        return $this->hasMany(Material::class, ['project_id' => 'id']);
    }

    /**
     * Связь с участниками проекта
     */
    public function getProjectUsers()
    {
        return $this->hasMany(ProjectUser::class, ['project_id' => 'id']);
    }

    /**
     * Связь с пользователями через участников
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->via('projectUsers');
    }

    /**
     * Получить участников проекта
     */
    public function getParticipants()
    {
        return $this->getProjectUsers()->with('user')->all();
    }

    /**
     * Проверить, является ли пользователь участником проекта
     */
    public function isUserParticipant($userId)
    {
        return $this->getProjectUsers()->where(['user_id' => $userId])->exists();
    }

    /**
     * Добавить пользователя к проекту
     */
    public function addUser($userId, $role = ProjectUser::ROLE_PARTICIPANT)
    {
        if (!$this->isUserParticipant($userId)) {
            $projectUser = new ProjectUser();
            $projectUser->project_id = $this->id;
            $projectUser->user_id = $userId;
            $projectUser->role = $role;
            return $projectUser->save();
        }
        return false;
    }

    /**
     * Удалить пользователя из проекта
     */
    public function removeUser($userId)
    {
        $projectUser = ProjectUser::findOne(['project_id' => $this->id, 'user_id' => $userId]);
        if ($projectUser) {
            return $projectUser->delete();
        }
        return false;
    }

    /**
     * Получить проекты, доступные пользователю
     */
    public static function getAccessibleProjects($userId, $isAdmin = false)
    {
        if ($isAdmin) {
            // Админы видят все проекты
            return self::find()->all();
        }

        // Обычные пользователи видят только свои проекты
        return self::find()
            ->innerJoinWith('projectUsers')
            ->where(['project_user.user_id' => $userId])
            ->all();
    }

    public function isUserOwner($userId)
    {
        return $this->getProjectUsers()
            ->where(['user_id' => $userId, 'role' => ProjectUser::ROLE_OWNER])
            ->exists();
    }
}