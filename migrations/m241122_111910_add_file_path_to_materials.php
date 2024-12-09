<?php

use yii\db\Migration;

/**
 * Class m241122_111910_add_file_path_to_materials
 */
class m241122_111910_add_file_path_to_materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp() {}

    public function up()
    {
        $this->addColumn('{{%materials}}', 'file_path', $this->string()->notNull());
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241122_111910_add_file_path_to_materials cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241122_111910_add_file_path_to_materials cannot be reverted.\n";

        return false;
    }
    */
}
