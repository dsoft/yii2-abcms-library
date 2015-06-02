<?php

namespace abcms\library\behaviors;

use Yii;
use yii\base\Behavior;
use yii\validators\Validator;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\base\ErrorException;

class FileUploadBehavior extends Behavior
{

    public $attribute = null;
    public $required = true;
    public $requiredOn = 'create';
    public $extensions = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if(!$this->attribute) {
            throw new InvalidConfigException('"attribute" property must be set.');
        }
        if(!$this->extensions) {
            throw new InvalidConfigException('"extensions" property must be set.');
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
        $extensions = $this->extensions;
        $fileValidator = Validator::createValidator('file', $owner, $attribute, ['extensions' => $extensions]);
        $validators->append($fileValidator);
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
                $fileName = $this->returnFileName();
                $folderName = $this->returnFolderName();
                $randomName = $fileName."_".time().mt_rand(10, 99).".".$file->extension;
                $directory = Yii::getAlias('@webroot/uploads/'.$folderName.'/');
                if(FileHelper::createDirectory($directory)) {
                    $mainFilePath = $directory.$randomName;
                    $file->saveAs($mainFilePath);
                    $owner->setAttribute($attribute, $randomName);
                }
                else {
                    throw new ErrorException('Unable to create directoy.');
                }
            }
        }
    }

    protected function returnFileName()
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

    public function returnFileLink($attribute = null)
    {
        $owner = $this->owner;
        $folderName = $this->returnFolderName();
        if(!$attribute){
            $attribute = $this->attribute;
        }
        $fileName = $owner->getAttribute($attribute);
        $return = null;
        if($fileName){
            $return =  Yii::getAlias('@web/uploads/'.$folderName.'/'.$fileName);
        }
        return $return;
    }

}
