<?php

namespace abcms\library\fields;

use yii\helpers\Html;

/**
 * Text Input Field
 */
class TextArea extends Field
{

    /**
     * @inheritdocs
     */
    public $inputOptions = ['class' => 'form-control', 'rows' => 6];

    /**
     * Renders field input
     */
    public function renderInput()
    {
        return Html::textarea($this->inputName, $this->value, $this->inputOptions);
    }

    /**
     * @inheritdocs
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

}
