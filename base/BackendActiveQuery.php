<?php

namespace abcms\library\base;

use yii\db\ActiveQuery;

class BackendActiveQuery extends ActiveQuery
{
    
    public $enableOrdering = true;

    public $enableDeleted = true;
    
    public $tableName = '';

    public function init()
    {
        $tableName = $this->tableName;
        $orderBy = ($this->enableOrdering) ? "$tableName.ordering ASC, $tableName.id DESC" : "$tableName.id DESC";
        $this->orderBy($orderBy);
        if($this->enableDeleted){
            $this->andWhere(["$tableName.deleted"=>0]);
        }
        parent::init();
    }

    public function active($state = true)
    {
        $tableName = $this->tableName;
        return $this->andWhere(["$tableName.active" => $state]);
    }

}
