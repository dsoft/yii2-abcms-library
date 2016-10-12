<?php

namespace abcms\library\behaviors;

use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\db\Expression;

/**
 * TimeBehavior automatically fills the 'time' attributes with the current time using NOW() expression on create and update.
 *
 * To use TimeBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use abcms\library\behaviors\TimeBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         TimestampBehavior::className(),
 *     ];
 * }
 * ```
 */
class TimeBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive time value on create and update
     */
    public $timeAttribute = 'time';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->timeAttribute,
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->timeAttribute,
            ];
        }
    }

    /**
     * @inheritdoc
     *
     * In case, when the [[value]] is `null`, NOW() expression will be used.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return new Expression('NOW()');
        }
        return parent::getValue($event);
    }
}
