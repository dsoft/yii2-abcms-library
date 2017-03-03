<?php

namespace abcms\library\fields;

use yii\helpers\Html;

/**
 * Text Input Field
 */
class Text extends Field
{
    
    /**
     * Renders field input
     */
    public function renderInput(){
        return Html::textInput($this->inputName, $this->value, $this->inputOptions);
    }
}
