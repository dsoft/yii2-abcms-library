<?php
namespace abcms\library\fields;

use Yii;

/**
 * URL Input Field
 */
class Url extends Text
{
    /**
     * {@inheritdoc}
     */
    public function addRulesToModel($model)
    {
        $options = ['message' => Yii::t('app', 'This field is not a valid URL.')];
        $model->addRule($this->inputName, 'url', $options);
    }
}
