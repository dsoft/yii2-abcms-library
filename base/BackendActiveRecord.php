<?php

namespace abcms\library\base;

use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use abcms\multilanguage\ActiveDataProvider;

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
     * @var string Name of the multilanguage component in the configuration
     */
    public static $multilanguageComponent = "multilanguage";

    /**
     * @inheritdoc
     * @return \abcms\library\base\BackendActiveQuery
     */
    public static function find()
    {
        $tableName = static::tableName();
        return new BackendActiveQuery(get_called_class(), [
            'enableDeleted' => static::$enableDeleted,
            'tableName' => $tableName,
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
        if(static::$enableDeleted) {
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
    
    /**
     * Find and return translated model
     * @param integer $id
     * @param boolean $active
     * @return BackendActiveRecord
     */
    public static function findTranslatedModel($id, $active = true){
        $model = self::findModel($id, $active);
        return $model->translate();
    }

    /**
     * Return the models that should be used in the frontend.
     * @param array $where
     * @param boolean|array $ordering
     * @param boolean $onlyTranslatedModels if false return all models even if not transled to the current language
     * @return BackendActiveRecord[]
     */
    public static function getFrontendModels($where = [], $ordering = true, $onlyTranslatedModels = true)
    {
        $query = self::getFrontendQuery($where, $ordering);
        $models = Yii::$app->get(static::$multilanguageComponent)->translateMultiple($query->all(), NULL, $onlyTranslatedModels);
        return $models;
    }

    /**
     * Return data provider that should be used in the frontend.
     * @param integer $pageSize
     * @param array $where
     * @param boolean $ordering
     * @return \app\models\ActiveDataProvider
     */
    public static function getFrontendDataProvider($pageSize = 8, $where = [], $ordering = true)
    {
        $query = self::getFrontendQuery($where, $ordering);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
        return $dataProvider;
    }
    
    /**
     * Return frontend query
     * @param array $where additioinal where array added to query
     * @param boolean|array $ordering if ordering field is enabled
     * @return BackendActiveQuery
     */
    public static function getFrontendQuery($where = [], $ordering = true)
    {
        $query = self::find()->active();
        if(is_array($ordering)){
            $query->orderBy($ordering);
        }
        else{
            if($ordering === true) {
                $query->orderBy('ordering ASC, id DESC');
            }
            else {
                $query->orderBy('id DESC');
            }
        }
        
        if($where) {
            $query->andWhere($where);
        }
        return $query;
    }

}
