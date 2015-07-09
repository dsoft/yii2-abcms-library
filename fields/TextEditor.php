<?php

namespace abcms\library\fields;

use yii\helpers\Html;

/**
 * Text Input Field
 */
class TextEditor extends Field
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
        return Html::activeTextarea($this->model, $this->attributeExpression, $this->inputOptions);
    }

    /**
     * @inheritdocs
     */
    public function detailViewAttribute()
    {
        $array = [
            'attribute' => $this->attribute,
            'value' => $this->renderValue(),
            'format'=> 'html',
        ];
        return $array;
    }

}
