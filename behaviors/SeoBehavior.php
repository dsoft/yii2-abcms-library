<?php

namespace abcms\library\behaviors;

use Yii;
use yii\helpers\StringHelper;

class SeoBehavior extends \yii\base\Behavior
{

    public $primaryKeyAttribute = 'id';
    public $titleAttribute = 'title';
    public $descriptionAttribute = 'description';
    public $route = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(!$this->route) {
            throw new InvalidConfigException('"route" property must be set.');
        }
    }

    public function getUrlTitle()
    {
        $owner = $this->owner;
        $title = $owner->{$this->titleAttribute};
        $title = strip_tags($title);
        $title = rtrim($title);      
        $title = str_replace(' ', '-', $title);
        $title = preg_replace('/[^\x{0600}-\x{06FF}A-Za-z0-9\-]/u', '', $title);
        $title = preg_replace('/-+/', '-', $title);
        if(!preg_match('/\p{Arabic}/u', $title)){
            $title = strtolower($title);
        }
        return $title;
    }

    public function getId()
    {
        return $this->owner->{$this->primaryKeyAttribute};
    }
    
    public function getTitle(){
        return $this->owner->{$this->titleAttribute};
    }
    
    public function getDescription(){
        return $this->owner->{$this->descriptionAttribute};
    }

    public function frontUrl($params = [])
    {
        $params[0]=$this->route;
        $params['id'] =  $this->id;
        $params['urlTitle'] =  $this->urlTitle;
        return \yii\helpers\Url::to($params);
    }
    
    public function tags(){
        $view = Yii::$app->view;
        $view->title = $this->getMetaTitle();
        if($this->descriptionAttribute){
            $view->registerMetaTag(['name' => 'description', 'content' => $this->getMetaDescription()], 'description');
        }
    }
    
    public function getMetaTitle(){
        return Yii::$app->name . ' - '.$this->title;
    }
    
    public function getMetaDescription(){ 
        $description = StringHelper::truncateWords(strip_tags($this->getDescription()), 25);
        return $description;
    }

}
