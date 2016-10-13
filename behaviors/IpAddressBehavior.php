<?php

namespace abcms\library\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

/**
 * IpAddressBehavior automatically fills the 'ipAddress' attributes with the user ip address on create.
 *
 * To use it, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use abcms\library\behaviors\IpAddressBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         IpAddressBehavior::className(),
 *     ];
 * }
 * ```
 */
class IpAddressBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive the ip address value on create and update
     */
    public $ipAttribute = 'ipAddress';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->ipAttribute,
            ];
        }
    }

    /**
     * @inheritdoc
     *
     * In case, when the [[value]] is `null`, ip address will be used.
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return Yii::$app->request->userIp;
        }
        return parent::getValue($event);
    }
}
