<?php

namespace abcms\library\behaviors;

use Yii;
use yii\helpers\StringHelper;
use abcms\library\helpers\Inflector;

class SeoBehavior extends \yii\base\Behavior
{

    public $primaryKeyAttribute = 'id';
    public $titleAttribute = 'title';
    public $descriptionAttribute = 'description';
    public $route = '';
    public $titlePrefix = '';
    public $titlePrefixSeparator = ' - ';
    public $titleSuffix = true;
    public $titleSuffixSeparator = ' | ';

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
        $title = Inflector::slug($title);
        return $title;
    }

    public function getId()
    {
        return $this->owner->{$this->primaryKeyAttribute};
    }

    public function getTitle()
    {
        return $this->owner->{$this->titleAttribute};
    }
    
    public function getDescription(){
        return $this->owner->{$this->descriptionAttribute};
    }

    public function getDescriptionText()
    {
        return strip_tags($this->getDescription());
    }

    public function frontUrl($params = [], $scheme = false)
    {
        $params[0] = $this->route;
        $params['id'] = $this->id;
        $params['urlTitle'] = $this->urlTitle;
        return \yii\helpers\Url::to($params, $scheme);
    }
    
    public function getFrontUrl(){
        return $this->frontUrl();
    }

    public function tags()
    {
        $this->registerTags();
    }

    public function registerTags()
    {
        $view = Yii::$app->view;
        $title = $this->getMetaTitle();
        $view->title = $title;
        $view->registerMetaTag(['property' => 'og:title', 'content' => $title], 'og:title');
        if($this->descriptionAttribute && $this->getDescriptionText()) {
            $description = $this->getMetaDescription();
            $view->registerMetaTag(['name' => 'description', 'content' => $description], 'description');
            $view->registerMetaTag(['property' => 'og:description', 'content' => $description], 'og:description');
        }
        $url = $this->frontUrl([], true);
        $view->registerLinkTag(['rel'=>'canonical', 'href'=>$url], 'canonical');
        $view->registerMetaTag(['property' => 'og:url', 'content' => $url], 'og:url');
    }

    public function getMetaTitle()
    {
        $title = $this->title;
        if($this->titlePrefix) {
            $title = $this->titlePrefix.$this->titlePrefixSeparator.$title;
        }
        if($this->titleSuffix) {
            $title .= $this->titleSuffixSeparator.Yii::$app->name;
        }
        $title = strip_tags($title);
        return $title;
    }

    public function getMetaDescription()
    {
        $description = StringHelper::truncateWords($this->getDescriptionText(), 25);
        return $description;
    }

}
