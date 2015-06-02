<?php

namespace abcms\library\behaviors;

use Yii;
use yii\base\Behavior;
use yii\validators\Validator;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\base\ErrorException;
use yii\imagine\Image;

class ImageUploadBehavior extends Behavior
{

    public $attribute = null;
    public $sizes = [];
    public $required = true;
    public $requiredOn = 'create';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(!$this->attribute) {
            throw new InvalidConfigException('"attribute" property must be set.');
        }
    }

    /**
     * @inheritdocs
     */
    public function attach($owner)
    {
        parent::attach($owner);
        $attribute = $this->attribute;
        $validators = $owner->getValidators();
        $imageValidator = Validator::createValidator('image', $owner, $attribute, ['extensions' => 'png, jpg']);
        $validators->append($imageValidator);
        if($this->required) {
            $options = [];
            if($this->requiredOn) {
                $options['on'] = $this->requiredOn;
            }
            $requiredValidator = Validator::createValidator('required', $owner, $attribute, $options);
            $validators->append($requiredValidator);
        }
    }

    /**
     * @inheritdocs
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            BaseActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
        ];
    }

    public function beforeValidate()
    {
        $owner = $this->owner;
        $attribute = $this->attribute;
        if($owner->isAttributeChanged($attribute)) {
            $file = UploadedFile::getInstance($owner, $attribute);
            if(!$file) { // to disable overwriting saved image on update if there's no new image
                $owner->setAttribute($attribute, $owner->getOldAttribute($attribute));
            }
            else {
                $owner->setAttribute($attribute, $file);
            }
        }
    }

    public function afterValidate()
    {
        $owner = $this->owner;
        $attribute = $this->attribute;
        if(!$owner->hasErrors()) {
            $file = UploadedFile::getInstance($owner, $attribute);
            if($file) {
                $fileName = $this->returnImageName();
                $folderName = $this->returnFolderName();
                $randomName = $fileName."_".time().mt_rand(10, 99).".".$file->extension;
                $directory = Yii::getAlias('@webroot/uploads/'.$folderName.'/');
                if(FileHelper::createDirectory($directory)) {
                    $mainImagePath = $directory.$randomName;
                    $file->saveAs($mainImagePath);
                    $owner->setAttribute($attribute, $randomName);
                    $this->saveSizes($directory, $randomName);
                }
                else {
                    throw new ErrorException('Unable to create directoy.');
                }
            }
        }
    }

    protected function saveSizes($mainFolder, $imageName)
    {
        $sizes = (array) $this->sizes;
        foreach($sizes as $name => $size) {
            if(isset($size['width'], $size['height'])) {
                $folderName = $mainFolder.$name.'/';
                if(FileHelper::createDirectory($folderName)) {
                    Image::thumbnail($mainFolder.$imageName, $size['width'], $size['height'])->save($folderName.$imageName, array('quality' => 95));
                }
                else {
                    throw new ErrorException('Unable to create directoy.');
                }
            }
        }
    }

    protected function returnImageName()
    {
        return $this->returnShortName();
    }

    protected function returnFolderName()
    {
        return $this->returnShortName();
    }

    protected function returnShortName()
    {
        $owner = $this->owner;
        $class = new \ReflectionClass($owner);
        $name = strtolower($class->getShortName());
        return $name;
    }

    public function returnImageLink($attribute = null, $size = null)
    {
        $owner = $this->owner;
        $folderName = $this->returnFolderName();
        if($size){
            $folderName .= "/$size";
        }
        if(!$attribute){
            $attribute = $this->attribute;
        }
        $imageName = $owner->getAttribute($attribute);
        $return = null;
        if($imageName){
            $return =  Yii::getAlias('@web/uploads/'.$folderName.'/'.$imageName);
        }
        return $return;
    }

}
