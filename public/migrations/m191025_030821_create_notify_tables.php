<?php

use yii\db\Migration;

/**
 * Class m191025_030821_create_notify_tables
 */
class m191025_030821_create_notify_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->createTable('{{%appNotify}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('Для кого оповещение'),
            'user_ct' => $this->integer()->notNull()->comment('Тот кто создал оповещение'),
            'datetime' => $this->string(50)->notNull()->comment('Время оповещения'),
            'date_ct' => $this->string(50)->notNull()->comment('Дата создания оповещения'),
            'type' => $this->integer()->null(),
            'text' => $this->text()->null()->comment('Текст оповещения'),
            'mail' => $this->integer()->null()->comment('Отправка на почту'),
            'repeat' => $this->integer()->defaultValue(1)->comment('Количество повторений'),
        ]);


        echo shell_exec("php yii gii/model --tableName=appNotify --modelClass=AppNotify --interactive=0 --overwrite=1");

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191025_030821_create_notify_tables cannot be reverted.\n";

        return false;
    }
    */
}
