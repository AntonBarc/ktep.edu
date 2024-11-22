<?php

namespace app\models;

use yii\db\ActiveRecord;

class Material extends \yii\db\ActiveRecord {
    public static function tableName() {
        return 'materials'; // Укажите вашу таблицу
    }

    public $file_path;

    public function rules() {
        return [
            [['content'], 'string'],
            [['title', 'file_path'], 'required'],
            [['file'], 'file', 'extensions' => 'doc, docx, xls, xlsx, pdf, txt'],
            [['file_path'], 'string'],
        ];
    }
}
