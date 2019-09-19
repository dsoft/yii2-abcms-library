<?php

namespace abcms\library\fields;

use Yii;

/**
 * Text Input Field
 */
class DropDown extends Field
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
        $field = $activeField->dropDownList($list, ['prompt' => Yii::t('app', '--Select--')]);
        return $field;
    }

}
