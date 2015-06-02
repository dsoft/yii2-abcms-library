<?php

namespace abcms\library\base;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

class BackendActiveRecord extends ActiveRecord
{

    public $enableTime = true;
    public static $enableOrdering = true;

    public function behaviors()
    {
        $array = [];
        if($this->enableTime) {
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
        return new BackendActiveQuery(get_called_class(), ['enableOrdering'=>static::$enableOrdering]);
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
        if(!$this->beforeDelete()) {
            return false;
        }
        $this->deleted = 1;
        $result = $this->save(false);
        $this->setOldAttributes(null);
        $this->afterDelete();
        return $result;
    }

    public static function findModel($id, $active = true)
    {
        $className = self::className();
        $condition = $id;
        if($active){
            $condition = ['id'=>$id, 'active'=>1];
        }
        if(($model = $className::findOne($condition)) !== null) {
            return $model;
        }
        else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
