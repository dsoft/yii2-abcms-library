<?php

namespace abcms\library\fields;

use Yii;

/**
 * Multiple choice select input field
 */
class MultipleChoiceSelect extends Field
{

    /**
     * {@inheritdoc}
     */
    public function renderInput()
    {
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function renderActiveField($activeField)
    {
        $activeField = parent::renderActiveField($activeField);
        $list = array_combine($this->list, $this->list);
        $field = $activeField->dropDownList($list, ['multiple' => 'multiple', 'size' => 6]);
        return $field;
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasMultipleAnswers()
    {
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function renderValue()
    {
        if(is_array($this->value)){
            return implode(', ', $this->value);
        }
        return $this->value;
    }

}
