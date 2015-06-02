<?php

namespace abcms\library\widgets\location;

use Yii;
use yii\widgets\InputWidget;

class Picker extends InputWidget
{
    
    /** @var string name of second attribute where location y will be saved, location x will be saved in [[attribute]] */
    public $attribute2;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        Asset::register($this->getView());
        echo $this->render('_map-location', [
            'model'=>$this->model,
            'attribute'=>$this->attribute,
            'attribute2'=>$this->attribute2,
        ]);
    }
}
