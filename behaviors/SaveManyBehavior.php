<?php

namespace abcms\library\behaviors;

use Yii;
use yii\db\BaseActiveRecord;

class SaveManyBehavior extends \yii\base\Behavior
{

    public $relation = null;
    public $className = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(!$this->relation) {
            throw new InvalidConfigException('"relation" property must be set.');
        }
        if(!$this->className) {
            throw new InvalidConfigException('"className" property must be set.');
        }
    }

    /**
     * @inheritdocs
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function afterSave()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $post = Yii::$app->request->post();
        $relation = $this->relation;
        $className = $this->className;
        if(isset($post[$owner->formName()][$relation])) {
            $formData = (array)$post[$owner->formName()][$relation];
            $owner->unlinkAll($relation, true);
            foreach($formData as $id) {
                $model = $className::findOne((int) $id);
                if($model) {
                    $owner->link($relation, $model);
                }
            }
        }
    }

}
