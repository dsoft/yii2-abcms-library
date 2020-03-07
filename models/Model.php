<?php

namespace abcms\library\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "model".
 *
 * @property integer $id
 * @property string $className
 */
class Model extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'model';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['className'], 'required'],
            [['className'], 'string', 'max' => 255]
        ];
    }
    
    /**
     * Return id of the provided classname in the model table, and create new entry if it doesn't exist
     * @param string $className
     * @return int ClassName id in the model table
     */
    public static function returnModelId($className){
        $model = self::find()->andWhere(['className'=>$className])->one();
        if(!$model){
            $model = new self;
            $model->className = $className;
            $model->save(false);
        }
        $id = $model->id;
        return $id;
    }
    
    /**
     * Return an array where id is the key and className is the value.
     * Can be used in drop down lists.
     * @return array
     */
    public static function getList()
    {
        $models = self::find()->orderBy(['id' => SORT_ASC])->all();
        return ArrayHelper::map($models, 'id', 'className');
    }
}
