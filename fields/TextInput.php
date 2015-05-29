<?php

namespace abcms\library\fields;

use yii\helpers\Html;

/**
 * Text Input Field
 */
class TextInput extends Field
{
    
    /**
     * Renders field input
     */
    public function renderInput(){
        return Html::activeTextInput($this->model, $this->attributeExpression, $this->inputOptions);
    }
}
