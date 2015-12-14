<?php

namespace abcms\library\fields;

use yii\helpers\Html;

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
        $attr = Html::getInputName($this->model, $this->attributeExpression);
        $settings = $this->settings;
        if($this->language == 'ar') {
            $settings['direction'] = 'rtl';
        }
        return \vova07\imperavi\Widget::widget([
                    'name' => $attr,
                    'value' => $this->value,
                    'settings' => $settings,
        ]);
    }

    /**
     * @inheritdocs
     */
    public function detailViewAttribute()
    {
        $array = [
            'attribute' => $this->attribute,
            'value' => $this->renderValue(),
            'format' => 'html',
        ];
        return $array;
    }

}
