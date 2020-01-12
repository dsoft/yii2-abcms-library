<?php

namespace abcms\library\fields;


/**
 * Text Input Field
 */
class TextEditor extends Field
{
    
    public $settings = [
            'minHeight' => 200,
        ];

    /**
     * Renders field input
     */
    public function renderInput()
    {
        $settings = $this->settings;
        return \vova07\imperavi\Widget::widget([
                    'name' => $this->inputName,
                    'value' => $this->value,
                    'settings' => $settings,
        ]);
    }
    
        /**
     * {@inheritdoc}
     */
    public function renderActiveField($activeField)
    {
        $activeField = parent::renderActiveField($activeField);
        $activeField->widget(\vova07\imperavi\Widget::className(), [
            'settings' => $this->settings,
        ]);
        return $activeField;
    }

    /**
     * @inheritdocs
     */
    public function getDetailViewAttribute()
    {
        $array = [
            'label' => $this->label,
            'value' => $this->renderValue(),
            'format' => 'html',
        ];
        return $array;
    }

}
