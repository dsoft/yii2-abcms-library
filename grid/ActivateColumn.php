<?php

namespace abcms\library\grid;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\Column;

class ActivateColumn extends Column
{

    /**
     * @var string the ID of the controller that should handle the actions specified here.
     * If not set, it will use the currently active controller. This property is mainly used by
     * [[urlCreator]] to create URLs for different actions. The value of this property will be prefixed
     * to each action name to form the route of the action.
     */
    public $controller;

    /**
     * @var callable a callback that creates a button URL using the specified model information.
     * The signature of the callback should be the same as that of [[createUrl()]].
     * If this property is not set, button URLs will be created using [[createUrl()]].
     */
    public $urlCreator;
    
    public $header = 'Activate';
    
    public $filterInputOptions = ['class' => 'form-control', 'id' => null];
    

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Creates a URL for the given action and model.
     * This method is called for the activate button
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        if($this->urlCreator instanceof Closure) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        }
        else {
            $params = is_array($key) ? $key : ['id' => (string) $key];
            $params[0] = $this->controller ? $this->controller.'/'.$action : $action;

            return Url::toRoute($params);
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $url = $this->createUrl('activate', $model, $key, $index);
        $color = ($model->active) ? 'green' : 'red';
        return Html::a('<span class="glyphicon glyphicon-off" style="color:'.$color.';"></span>', $url, [
                    'title' => Yii::t('yii', 'Activate'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
        ]);
    }

    protected function renderFilterCellContent()
    {
        $model = $this->grid->filterModel;
        $options = array_merge(['prompt' => ''], $this->filterInputOptions);
        return Html::activeDropDownList($model, 'active', [0=>'Not Active', 1=>'Active'], $options);
        
    }

}
