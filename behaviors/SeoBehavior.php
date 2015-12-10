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

    public function getDescription()
    {
        return $this->owner->{$this->descriptionAttribute};
    }

    public function frontUrl($params = [])
    {
        $params[0] = $this->route;
        $params['id'] = $this->id;
        $params['urlTitle'] = $this->urlTitle;
        return \yii\helpers\Url::to($params);
    }

    public function tags()
    {
        $view = Yii::$app->view;
        $view->title = $this->getMetaTitle();
        if($this->descriptionAttribute) {
            $view->registerMetaTag(['name' => 'description', 'content' => $this->getMetaDescription()], 'description');
        }
    }

    public function getMetaTitle()
    {
        $title = $this->title;
        if($this->titlePrefix) {
            $title = $this->titlePrefix.$this->titlePrefixSeparator.$title;
        }
        if($this->titleSuffix) {
            $title .= ' - '.Yii::$app->name;
        }
        $title = strip_tags($title);
        return $title;
    }

    public function getMetaDescription()
    {
        $description = StringHelper::truncateWords(strip_tags($this->getDescription()), 25);
        return $description;
    }

    /**
     * Check if website is multi language
     * @return boolean
     */
    public function isMultiLanguage()
    {
        $result = false;
        if(isset(Yii::$app->params['seo']['isMultiLanguage'])) {
            $result = Yii::$app->params['seo']['isMultiLanguage'];
        }
        return $result;
    }

}
