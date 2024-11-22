<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%materials}}`.
 */
class m241121_164525_create_materials_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('materials', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'file' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%materials}}');
    }
    
}
