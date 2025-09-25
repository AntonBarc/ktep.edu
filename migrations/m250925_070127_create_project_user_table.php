<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%project_user}}`.
 */
class m250925_070127_create_project_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('project_user', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'role' => $this->string(20)->notNull()->defaultValue('participant'),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Внешние ключи
        $this->addForeignKey(
            'fk-project_user-project_id',
            'project_user',
            'project_id',
            'projects', // Указываем правильную таблицу
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-project_user-user_id',
            'project_user',
            'user_id',
            'users', // Убедитесь что таблица users существует
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Уникальный индекс чтобы пользователь не мог быть добавлен в проект дважды
        $this->createIndex(
            'idx-project_user-unique',
            'project_user',
            ['project_id', 'user_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-project_user-project_id', 'project_user');
        $this->dropForeignKey('fk-project_user-user_id', 'project_user');
        $this->dropTable('project_user');
    }
}
