<?php

namespace abcms\library\base;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

class BackendActiveRecord extends ActiveRecord
{

    public static $enableTime = true;
    public static $enableDeleted = true;

    public function behaviors()
    {
        $array = [];
        if(static::$enableTime) {
            $array[] = [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['time'],
                ],
                'value' => new Expression('NOW()'),
            ];
        }
        return $array;
    }

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
