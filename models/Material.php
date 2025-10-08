<?php

namespace app\models;

use yii\db\ActiveRecord;

class Material extends ActiveRecord
{
    const TYPE_FOLDER = 'folder';
    const TYPE_COURSE = 'course';
    const TYPE_TRAJECTORY = 'trajectory';
    const TYPE_LONGREAD = 'longread';
    const TYPE_TEST = 'test';
    const TYPE_ASSIGNMENT = 'assignment';
    const TYPE_LINK = 'link';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'materials';
    }

    public static function typesList()
    {
        return [
            self::TYPE_FOLDER => 'Папка',
            self::TYPE_COURSE => 'Курс',
            self::TYPE_TRAJECTORY => 'Траектория обучения',
            self::TYPE_LONGREAD => 'Лонгрид',
            self::TYPE_TEST => 'Тест',
            self::TYPE_ASSIGNMENT => 'Задание',
            self::TYPE_LINK => 'Ссылка',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id'], 'integer'],
            [['file'], 'file', 'extensions' => 'doc, docx, pdf', 'maxSize' => 10 * 1024 * 1024],
            [['title'], 'string'], // Название файла автоматически задается
            [['type'], 'in', 'range' => array_keys(self::typesList())],
            [['parent_id'], 'integer'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Material::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    // Связь: материалы в этой папке
    public function getChildren()
    {
        return $this->hasMany(Material::class, ['parent_id' => 'id']);
    }

    // Связь: родительская папка
    public function getParent()
    {
        return $this->hasOne(Material::class, ['id' => 'parent_id']);
    }

    // Является ли материал папкой?
    public function isFolder()
    {
        return $this->type === self::TYPE_FOLDER;
    }

    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }
    public function getAuthor()
    {
        return $this->hasOne(\app\models\User::class, ['id' => 'author_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'file_path' => 'Путь к файлу',
            'created_at' => 'Дата добавления',
            'file' => 'Файл',
        ];
    }
}
/* */