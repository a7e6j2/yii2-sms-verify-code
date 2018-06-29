<?php

use yii\db\Migration;

/**
 * Class m171205_080935_create_table_verify_code
 */
class m171205_080935_create_table_verify_code extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable("{{%sms_verify_code}}", [
            'id' => $this->primaryKey()->unsigned(),
            'mobile' => $this->bigInteger()->unsigned()->notNull()->comment('手机号'),
            'ip' => $this->string(100)->notNull()->defaultValue('')->comment('IP 地址'),
            'code' => $this->string(15)->notNull()->comment('验证码'),
            'scenario' => $this->string(30)->defaultValue('')->comment('场景'),
            'sent_time' => $this->bigInteger()->unsigned()->defaultValue(0)->comment('发送时间'),
            'success' => $this->smallInteger()->defaultValue(0)->comment('是否成功'),
            'remark' => $this->text()->comment('备注'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return $this->dropTable("{{%sms_verify_code}}");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171205_080935_create_table_verify_code cannot be reverted.\n";

        return false;
    }
    */
}
