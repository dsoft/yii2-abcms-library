<?php

namespace abcms\library\fields;

use yii\base\Object;
use yii\helpers\Inflector;

/**
 * Field is the base class of all Dynamic Fields/Input classes.
 */
abstract class Field extends Object
{

    /**
     * Value of the field
     * @var string
     */
    public $value;

    /**
     * Model that this field belongs to
     * @var \yii\base\Model
     */
    public $model;

    /**
     * Field attribute name
     * @var string
     */
    public $attribute;

    /**
     * @var string
     * Will be used as input name property
     */
    public $attributeExpression;

    /**
     * @var array
     * The options used to render the html input
     */
    public $inputOptions = ['class' => 'form-control'];
    
    /**
     * @var string
     * Language of the data entered in this field
     */
    public $language = null;

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
     * Return the array that should be used inside in the Detail View Widget 'attributes' property
     * @return array
     */
    public function detailViewAttribute()
    {
        $array = [
            'attribute' => $this->attribute,
            'value'=> $this->renderValue(),
        ];
        return $array;
    }

    /**
     * Receive string or array and transform it to an array that can be used to call Yii::createObject() to create a Field class
     * The attribute must be specified in the format of "attribute", "attribute:type" or as an array.
     * Type in "attribute:type" represents the classname as Id, like: text-input, text-area...
     * If a field is of class [[TextInput]], the "class" element can be omitted.
     * @param string $attribute
     * @return array
     * @throws InvalidConfigException
     */
    public static function normalizeObject($attribute)
    {
        if(is_string($attribute)) {
            if(!preg_match('/^([^:]+)(:(.*))?$/', $attribute, $matches)) {
                throw new InvalidConfigException('The attribute must be specified in the format of "attribute", "attribute:type"');
            }
            $class = isset($matches[3]) ? '\abcms\library\fields\\'.Inflector::id2camel($matches[3]) : TextInput::className();
            $array = [
                'class' => $class,
                'attribute' => $matches[1],
            ];
            return $array;
        }
        if(is_array($attribute)) {
            if(!isset($attribute['class'])) {
                $attribute['class'] = TextInput::className();
            }
            return $attribute;
        }
        throw new InvalidConfigException('The attribute must be specified in the format of "attribute", "attribute:type" or as an array');
    }
    
    /**
     * Validate value
     * @return boolean
     */
    public function validate(){
        return true;
    }

}
