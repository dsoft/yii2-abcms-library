<?php

namespace abcms\library\fields;

use yii\base\BaseObject;
use yii\helpers\Html;
use yii\base\DynamicModel;

/**
 * Field is the base class of all Dynamic Fields/Input classes.
 */
abstract class Field extends BaseObject
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
     * @var string Hint for the field
     */
    public $hint;
    
    /**
     * @var array List that can be used in drop down list and checkbox
     */
    public $list = [];
    
    /**
     * Additional data used to configure each field type
     * @var array
     */
    public $additionalData = [];
    
    /**
     * @var boolean If input is required
     */
    public $isRequired;
    
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
        $activeField->label($this->label);
        if($this->hint){
            $activeField->hint($this->hint);
        }
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
    
    /**
     * Return if field has multiple answers like a multi choice select box
     * @return boolean
     */
    public function hasMultipleAnswers()
    {
        return false;
    }
    
    /**
     * Create a DynamicModel from `$fields`
     * @param Field[] $fields
     * @return DynamicModel
     */
    public static function getDynamicModel($fields)
    {
        $attributesNames = [];
        $requiredAttributes = [];
        $safeAttributes = [];
        foreach($fields as $field)
        {
            if($field->value){
                $attributesNames[$field->inputName] = $field->value;
            }
            else{
                $attributesNames[] = $field->inputName;
            }
            if($field->isSafe()){
                $safeAttributes[] = $field->inputName;
                if($field->isRequired){
                    $requiredAttributes[] = $field->inputName;
                }
            }
        }
        $model = new DynamicModel($attributesNames);
        $model->addRule($safeAttributes, 'safe');
        if($requiredAttributes){
            $model->addRule($requiredAttributes, 'required', ['message' => Yii::t('abcms.multilanguage', 'This field cannot be left empty')]);
        }
        foreach($fields as $field){
            $field->addRulesToModel($model);
        }
        return $model;
    }

}
