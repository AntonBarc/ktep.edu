<?php

namespace app\models;

use yii\db\ActiveRecord;

class Material extends ActiveRecord
{
    public static function tableName()
    {
        return 'materials'; // Убедитесь, что у вас есть таблица "materials" в базе данных
    }

    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            ['author_id', 'integer'],
            ['created_at', 'safe'],
        ];
    }
}
