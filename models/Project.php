<?php

namespace app\models;

use yii\db\ActiveRecord;

class Project extends ActiveRecord
{
    public static function tableName()
    {
        return 'projects';
    }

    public function getMaterials()
    {
        return $this->hasMany(Material::class, ['project_id' => 'id']);
    }
}
