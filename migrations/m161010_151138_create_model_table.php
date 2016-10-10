<?php

use yii\db\Migration;

/**
 * Handles the creation for table `model`.
 * ./yii migrate --migrationPath=@vendor/abcms/yii2-library/migrations
 */
class m161010_151138_create_model_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('model', [
            'id' => $this->primaryKey(),
            'className' => $this->string()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('model');
    }
}
