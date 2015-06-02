<?php

namespace abcms\library\base;

use yii\db\ActiveQuery;

class BackendActiveQuery extends ActiveQuery
{
    
    public $enableOrdering = true;

    public $enableDeleted = true;

    public function init()
    {
        $orderBy = ($this->enableOrdering) ? 'ordering ASC, id DESC' : 'id DESC';
        $this->orderBy($orderBy);
        if($this->enableDeleted){
            $this->andWhere(['deleted'=>0]);
        }
        parent::init();
    }

    public function active($state = true)
    {
        return $this->andWhere(['active' => $state]);
    }

}
