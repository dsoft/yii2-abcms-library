<?php
namespace abcms\library\fields;

use Yii;

/**
 * Integer Input Field
 */
class Integer extends Text
{
    /**
     * {@inheritdoc}
     */
    public function addRulesToModel($model)
    {
        $options = ['message' => Yii::t('app', 'This field must be an integer.')];
        $additionalData = $this->additionalData;
        if(isset($additionalData['min']) && $additionalData['min'] && is_numeric($additionalData['min'])){
            $options['min'] = $additionalData['min'];
            $options['tooSmall'] = Yii::t('yii', 'This field must be no less than {min}.');
        }
        if(isset($additionalData['max']) && $additionalData['max'] && is_numeric($additionalData['max'])){
            $options['max'] = $additionalData['max'];
            $options['tooBig'] = Yii::t('yii', 'This field must be no greater than {max}.');
        }
        $model->addRule($this->inputName, 'integer', $options);
    }
}
