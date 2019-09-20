<?php

namespace abcms\library\fields;

use yii\helpers\Html;
use Yii;

/**
 * Text Input Field
 */
class TextArea extends Field
{

    /**
     * {@inheritdoc}
     */
    public $inputOptions = ['class' => 'form-control', 'rows' => 6];

    /**
     * {@inheritdoc}
     */
    public function renderInput()
    {
        return Html::textarea($this->inputName, $this->value, $this->inputOptions);
    }
    
    /**
     * {@inheritdoc}
     */
    public function renderActiveField($activeField)
    {
        $activeField = parent::renderActiveField($activeField);
        $field = $activeField->textarea($this->inputOptions);
        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetailViewAttribute()
    {
        $array = [
            'label' => $this->label,
            'value' => $this->renderValue(),
            'format'=> 'ntext',
        ];
        return $array;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addRulesToModel($model)
    {
        $additionalData = $this->additionalData;
        if(isset($additionalData['max']) && $additionalData['max'] && is_numeric($additionalData['max'])){
            $model->addRule($this->inputName, 'string', [
                'max'=> $additionalData['max'],
                'tooLong' => Yii::t('app', 'This field should contain at most {max, number} characters.'),
                ]);
        }
    }

}
