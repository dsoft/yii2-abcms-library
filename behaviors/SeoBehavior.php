<?php

namespace abcms\library\behaviors;

use Yii;
use yii\helpers\StringHelper;
use abcms\library\helpers\Inflector;
use yii\helpers\Url;

/**
 * SEO Behavior, used to register meta tags.
 * 
 * To use it, insert the following code to your ActiveRecord class and call $model->registerTags():
 *
 * ```php
 *
 * public function behaviors()
 * {
 *     return [
 *        [
 *               'class' => \abcms\library\behaviors\SeoBehavior::className(),
 *        ],
 *     ];
 * }
 * ```
 * 
 */
class SeoBehavior extends \yii\base\Behavior
{

    /**
     * @var string Primary key attribute
     */
    public $primaryKeyAttribute = 'id';

    /**
     * @var string Title attribute name
     */
    public $titleAttribute = 'title';

    /**
     * @var string Description attribute name
     */
    public $descriptionAttribute = 'description';

    /**
     * @var string Frontend page route
     */
    public $route = '';

    /**
     * @var string Title prefix
     */
    public $titlePrefix = '';

    /**
     * @var string Title prefix separator
     */
    public $titlePrefixSeparator = ' - ';

    /**
     * @var string|true Title suffix. App name will be used if set to true.
     */
    public $titleSuffix = true;

    /**
     * @var string Title suffix separator
     */
    public $titleSuffixSeparator = ' | ';

    /**
     * @var boolean If SEO widget in abcms/yii2-structure is used
     */
    public $useSeoCustomFields = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Returns the url title: the string that will be used in the url.
     * @return string
     */
    public function getUrlTitle()
    {
        $title = Inflector::slug($this->getTitle());
        return $title;
    }

    /**
     * Get the ID of the owner
     * @return string
     */
    public function getId()
    {
        return $this->owner->{$this->primaryKeyAttribute};
    }
    
    /**
     * @var array|null
     */
    private $_seoCustomFields = null;
    
    /**
     * Returns the SEO custom fields from SEO widget in yii2-structure
     * @return array|null
     */
    public function getSeoCustomFields()
    {
        if($this->_seoCustomFields === null){
            $customFields = $this->owner->getCustomFields();
            if(isset($customFields['seo'])){
                $this->_seoCustomFields = $customFields['seo'];
            }
        }
        return $this->_seoCustomFields;
    }

    /**
     * Get the title of the owner
     * @return string
     */
    public function getTitle()
    {
        $title = $this->owner->{$this->titleAttribute};
        if ($this->useSeoCustomFields) {
            $customFields = $this->getSeoCustomFields();
            if(isset($customFields['metaTitle']) && $customFields['metaTitle']){
                $title = $customFields['metaTitle'];
            }
        }
        return $title;
    }

    /**
     * Get the description of the owner
     * @return string
     */
    public function getDescription()
    {
        $description = $this->owner->{$this->descriptionAttribute};
        if ($this->useSeoCustomFields) {
            $customFields = $this->getSeoCustomFields();
            if(isset($customFields['metaDescription']) && $customFields['metaDescription']){
                $description = $customFields['metaDescription'];
            }
        }
        return $description;
    }

    /**
     * Returns the frontpage url
     * @param type $params
     * @param type $scheme
     * @return type
     */
    public function frontUrl($params = [], $scheme = false)
    {
        if ($this->route) {
            $params[0] = $this->route;
            $params['id'] = $this->id;
            $params['urlTitle'] = $this->urlTitle;
            return Url::to($params, $scheme);
        }
        return null;
    }

    /**
     * @see frontUrl()
     */
    public function getFrontUrl()
    {
        return $this->frontUrl();
    }

    /**
     * @see registerTags()
     */
    public function tags()
    {
        $this->registerTags();
    }

    /**
     * Register the meta tags
     */
    public function registerTags()
    {
        $view = Yii::$app->view;

        // Set title
        $title = $this->getMetaTitle();
        $view->title = $title;
        $view->registerMetaTag(['property' => 'og:title', 'content' => $title], 'og:title');

        // Register description tag
        if ($this->descriptionAttribute && $this->getDescription()) {
            $description = $this->getMetaDescription();
            $view->registerMetaTag(['name' => 'description', 'content' => $description], 'description');
            $view->registerMetaTag(['property' => 'og:description', 'content' => $description], 'og:description');
        }
        
        // Register keywords tag
        if ($keywords = $this->getMetaKeywords()) {
            $view->registerMetaTag(['name' => 'keywords', 'content' => $keywords], 'keywords');
        }

        // Register url tags
        $url = $this->frontUrl([], true);
        if($url){
            $view->registerLinkTag(['rel' => 'canonical', 'href' => $url], 'canonical');
            $view->registerMetaTag(['property' => 'og:url', 'content' => $url], 'og:url');
        }
    }

    /**
     * Return the title with prefix and suffix
     * @return string
     */
    public function getMetaTitle()
    {
        $title = $this->title;
        if ($this->titlePrefix) {
            $title = $this->titlePrefix . $this->titlePrefixSeparator . $title;
        }
        if ($this->titleSuffix) {
            $title .= $this->titleSuffixSeparator . Yii::$app->name;
        }
        $title = strip_tags($title);
        return $title;
    }

    /**
     * Get description that can be used in meta tag
     * @return string
     */
    public function getMetaDescription()
    {
        $description = StringHelper::truncateWords(strip_tags($this->getDescription()), 25);
        return $description;
    }
    
    /**
     * Get the keywords
     * @return string
     */
    public function getMetaKeywords()
    {
        $keywords = "";
        if ($this->useSeoCustomFields) {
            $customFields = $this->getSeoCustomFields();
            if(isset($customFields['metaKeywords']) && $customFields['metaKeywords']){
                $keywords = $customFields['metaKeywords'];
            }
        }
        return $keywords;
    }

}
