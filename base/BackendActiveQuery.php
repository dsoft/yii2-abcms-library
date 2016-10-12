<?php

namespace abcms\library\base;

use yii\db\ActiveQuery;

class BackendActiveQuery extends ActiveQuery
{

    public $enableDeleted = true;
    
    public $tableName = '';

    public function init()
    {
        parent::init();
        $tableName = $this->tableName;
        if($this->enableDeleted){
            $this->andWhere(["$tableName.deleted"=>0]);
        }
    }

    public function active($state = true)
    {
        $tableName = $this->tableName;
        return $this->andWhere(["$tableName.active" => $state]);
    }

}
