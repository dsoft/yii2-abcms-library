<?php

namespace abcms\library\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\base\Event;
use yii\helpers\HtmlPurifier;

/**
 * PurifyBehavior automatically purify the specified attribute before saving
 *
 * To use it, insert the following code to your ActiveRecord class:
 * 
 * ```php
 * 
 * use abcms\library\behaviors\PurifyBehavior;
 * 
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => PurifyBehavior::className(),
 *             'attributes' => ['title', 'description'],
 *         ],
 *     ];
 * }
 * ```
 * 
 */
class PurifyBehavior extends Behavior
{

    /**
     * @var array Array of attributes that should be purified before save
     */
    public $attributes = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(!is_array($this->attributes)) {
            throw new InvalidConfigException('"attributes" property must be an array.');
        }
    }

    /**
     * @inheritdocs
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'purifyAttributes',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'purifyAttributes',
        ];
    }

    /**
     * Purify the model attributes.
     * @param Event $event
     */
    public function purifyAttributes($event)
    {
        $attributes = $this->attributes;
        foreach($attributes as $attribute) {
            if(is_string($attribute)) {
                $this->owner->$attribute = HtmlPurifier::process($this->owner->$attribute);
            }
        }
    }

}
