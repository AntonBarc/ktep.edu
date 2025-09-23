<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'role'], 'required', 'on' => 'create'],
            [['username', 'password', 'authKey', 'accessToken', 'role'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['password'], 'string', 'min' => 6, 'on' => 'create'],
            [['role'], 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Пароль',
            'authKey' => 'Auth Key',
            'accessToken' => 'Access Token',
            'role' => 'Роль',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                // Генерируем authKey и accessToken для новых пользователей
                $this->authKey = Yii::$app->security->generateRandomString();
                $this->accessToken = Yii::$app->security->generateRandomString();
                
                // Хешируем пароль только если он был изменен
                if ($this->password && !empty($this->password)) {
                    $this->password = Yii::$app->security->generatePasswordHash($this->password);
                }
            } else {
                // При обновлении хешируем пароль только если он был изменен
                if ($this->isAttributeChanged('password') && !empty($this->password)) {
                    $this->password = Yii::$app->security->generatePasswordHash($this->password);
                }
            }
            return true;
        }
        return false;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Получить список ролей для dropdown
     */
    public static function getRolesList()
    {
        return [
            self::ROLE_USER => 'User',
            self::ROLE_ADMIN => 'Admin'
        ];
    }
}