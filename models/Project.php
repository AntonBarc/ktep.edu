<?php

namespace app\models;

use yii\db\ActiveRecord;

class Project extends ActiveRecord
{
    public static function tableName()
    {
        return 'projects';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название проекта',
        ];
    }
    
    public function getMaterials()
    {
        return $this->hasMany(Material::class, ['project_id' => 'id']);
    }
}
