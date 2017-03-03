<?php

namespace abcms\library\fields;

use yii\base\Object;
use yii\helpers\Html;

/**
 * Field is the base class of all Dynamic Fields/Input classes.
 */
abstract class Field extends Object
{
    
    /**
     * Name attribute of the input
     * @var string
     */
    public $inputName;

    /**
     * Value of the field
     * @var string
     */
    public $value;
    
    /**
     * @var string Title of the field
     */
    public $label;

    /**
     * @var array
     * The options used to render the html input
     */
    public $inputOptions = ['class' => 'form-control'];
    

    /**
     * Renders field input
     */
    abstract public function renderInput();

    /**
     * Return the formatted value
     * Used to display the field value in the Detail View Widget
     */
    public function renderValue()
    {
        return $this->value;
    }
    
    /**
     * Render the form input label
     */
    public function renderLabel()
    {
        return Html::label($this->label, $this->inputName);
    }
    
    /**
     * Renders the full field: container, input and label
     * @return string
     */
    public function renderField()
    {
        $html = Html::beginTag('div', ['class'=>'form-group']);
        $html .= $this->renderLabel();
        $html .= $this->renderInput();
        $html .= Html::endTag('div');
        return $html;
    }

    /**
     * Return the array that should be used inside in the Detail View Widget 'attributes' property
     * @return array
     */
    public function detailViewAttribute()
    {
        $array = [
            'label' => $this->label,
            'value'=> $this->renderValue(),
        ];
        return $array;
    }
    
    /**
     * Validate value
     * @return boolean
     */
    public function validate(){
        return true;
    }

}
