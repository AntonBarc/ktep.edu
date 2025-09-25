<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "project_user".
 *
 * @property int $id
 * @property int $project_id
 * @property int $user_id
 * @property string $role
 * @property string $created_at
 *
 * @property Project $project
 * @property User $user
 */
class ProjectUser extends ActiveRecord
{
    const ROLE_OWNER = 'owner';
    const ROLE_PARTICIPANT = 'participant';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'user_id'], 'required'],
            [['project_id', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['role'], 'string', 'max' => 20],
            [['role'], 'default', 'value' => self::ROLE_PARTICIPANT],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Проект',
            'user_id' => 'Пользователь',
            'role' => 'Роль',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[Project]].
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * Gets query for [[User]].
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Получить список ролей
     */
    public static function getRolesList()
    {
        return [
            self::ROLE_PARTICIPANT => 'Участник',
            self::ROLE_OWNER => 'Владелец',
        ];
    }
}