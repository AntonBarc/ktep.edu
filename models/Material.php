<?php

namespace app\models;

use yii\db\ActiveRecord;

class Material extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'doc, docx, pdf', 'maxSize' => 10 * 1024 * 1024],
            [['title'], 'string'], // Название файла автоматически задается
        ];
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