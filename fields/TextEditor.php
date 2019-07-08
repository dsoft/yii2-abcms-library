<?php

namespace abcms\library\fields;


/**
 * Text Input Field
 */
class TextEditor extends Field
{

    /**
     * @inheritdocs
     */
    public $inputOptions = ['class' => 'form-control', 'rows' => 6];
    
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
