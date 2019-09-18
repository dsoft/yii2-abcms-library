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
     * Returns the active field
     * @param \yii\widgets\ActiveField $activeField ActiveField Object
     * @return \yii\widgets\ActiveField|string
     */
    public function renderActiveField($activeField)
    {
        return $activeField;
    }

    /**
     * Return the array that should be used inside in the Detail View Widget 'attributes' property
     * @return array
     */
    public function getDetailViewAttribute()
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
    
    /**
     * Add custom validation rules to the provided model
     * @param \yii\base\DynamicModel $model
     */
    public function addRulesToModel($model)
    {
        
    }
    
    /**
     * Return if field is safe for mass assignment
     * @return boolean
     */
    public function isSafe()
    {
        return true;
    }

}
