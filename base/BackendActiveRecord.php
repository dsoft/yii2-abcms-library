<?php

namespace abcms\library\base;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * BackendActiveRecord is the base class for models that contains common backend features: soft delete, activate/deactivate...
 */
class BackendActiveRecord extends ActiveRecord
{
    /**
     * @var boolean true if soft removal is enabled and deleted attribute available
     */
    public static $enableDeleted = true;

    /**
     * @inheritdoc
     * @return \abcms\library\base\BackendActiveQuery
     */
    public static function find()
    {
        $tableName = static::tableName();
        return new BackendActiveQuery(get_called_class(), [
            'enableDeleted' => static::$enableDeleted,
            'tableName'=>$tableName,
        ]);
    }

    /**
     * Activate or Deactivate Model
     * @return ActiveRecord current model
     */
    public function activate()
    {
        if($this->active == 1) {
            $this->active = 0;
        }
        else {
            $this->active = 1;
        }
        return $this;
    }

    /**
     * Overwrites delete function to execute soft removal if [[enableDeleted]] is true
     * @return boolean
     */
    public function delete()
    {
        if(self::$enableDeleted) {
            if(!$this->beforeDelete()) {
                return false;
            }
            $this->deleted = 1;
            $result = $this->save(false);
            $this->setOldAttributes(null);
            $this->afterDelete();
            return $result;
        }
        else {
            return parent::delete();
        }
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param boolean $active if we should find active models only
     * @return BackendActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function findModel($id, $active = true)
    {
        $className = self::className();
        $condition = $id;
        if($active) {
            $condition = ['id' => $id, 'active' => 1];
        }
        if(($model = $className::findOne($condition)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
