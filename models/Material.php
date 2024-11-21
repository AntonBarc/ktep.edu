<?php

namespace app\models;

use yii\db\ActiveRecord;

class Material extends ActiveRecord
{
    public static function tableName()
    {
        return 'materials';
    }

    public function rules()
    {
        return [
            [['name', 'type', 'author_id'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 100],
            [['author_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }
}
