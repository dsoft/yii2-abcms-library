<?php

namespace abcms\library\grid;

use Yii;
use yii\grid\GridView;

class InlineFormGridView extends GridView
{

    /**
     * @inheritdoc
     */
    public $showFooter = true;

    /**
     * @var string HTML id of the footer row
     */
    public $footerRowId = 'inline-form';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        if (!$this->footerRowId && !is_string($this->footerRowId)) {
            throw new InvalidConfigException('Please specify a valid "footerRowId" property.');
        }  
        if(!isset($this->footerRowOptions['id'])) {
            $this->footerRowOptions['id'] = $this->footerRowId;
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {        
        $id = $this->footerRowOptions['id'];
        $view = $this->getView();
        InlineFormGridViewAsset::register($view);
        $view->registerJs("jQuery('#$id').abcmsInlineFormGridView();");
        parent::run();
    }

}
