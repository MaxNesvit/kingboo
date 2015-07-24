<?php

use yii\db\Schema;
use yii\db\Migration;

class m150424_151305_rel_room_facility extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%rel_room_facility}}', [
            'room_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'facility_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        $this->createIndex('uniq', '{{%rel_room_facility}}', ['room_id', 'facility_id'], true);
    }

    public function down()
    {
        $this->dropTable('{{%rel_room_facility}}');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
