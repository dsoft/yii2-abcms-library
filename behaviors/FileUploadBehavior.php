<?php

namespace abcms\library\behaviors;

use Yii;
use yii\base\Behavior;
use yii\validators\Validator;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;

/**
 * FileUploadBehavior automatically validate and upload the available file for a certain attribute.
 *
 * To use it, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use abcms\library\behaviors\FileUploadBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *          [
 *              FileUploadBehavior::className(),
 *              'attribute' => 'cv',
 *              'extensions' => 'pdf, doc, docx',
 *          ],
 *     ];
 * }
 * ```
 */
class FileUploadBehavior extends Behavior
{

    /**
     * @var string File attribute name
     */
    public $attribute = null;

    /**
     * @var boolean If file is required
     */
    public $required = true;

    /**
     * @var string|null On which scenario the file field is required.
     */
    public $requiredOn = null;

    /**
     * @var string List of allowed extensions.
     */
    public $extensions = '';

    /**
     * @var boolean Whether to check file type (extension) with mime-type on validation.
     */
    public $checkExtensionByMimeType = true;

    /**
     * @var string Validator type
     */
    protected $validatorType = 'file';

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
        $fileValidator = Validator::createValidator($this->validatorType, $owner, $attribute, ['extensions' => $extensions, 'checkExtensionByMimeType' => $this->checkExtensionByMimeType]);
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

    /**
     * Function that will run before the model validation validation.
     * It populates the file attribute.
     */
    public function beforeValidate()
    {
        $owner = $this->owner;
        $attribute = $this->attribute;
        $file = UploadedFile::getInstance($owner, $attribute);
        if(!$file) {
            if($owner->isAttributeChanged($attribute)){ // to disable overwriting saved file on update if there's no new file
                 $owner->setAttribute($attribute, $owner->getOldAttribute($attribute));
            }
        }
        else {
            $owner->setAttribute($attribute, $file);
        }
    }

    /**
     * Function that will run after the model validation.
     * It saves the file.
     */
    public function afterValidate()
    {
        $owner = $this->owner;
        $attribute = $this->attribute;
        if(!$owner->hasErrors()) {
            $file = UploadedFile::getInstance($owner, $attribute);
            if($file) {
                $fileName = $this->returnNewFileName().".".$file->extension;
                $folderName = $this->returnFolderName();
                $directory = Yii::getAlias('@webroot/uploads/'.$folderName.'/');
                if(FileHelper::createDirectory($directory)) {
                    $mainFilePath = $directory.$fileName;
                    $file->saveAs($mainFilePath);
                    $owner->setAttribute($attribute, $fileName);
                    $this->afterFileSave($directory, $fileName);
                }
                else {
                    throw new ErrorException('Unable to create directoy.');
                }
            }
        }
    }

    /**
     * Function called after saving the files, can be overwritten in children classes
     * @param string $directory
     * @param string $fileName
     */
    protected function afterFileSave($directory, $fileName)
    {
        
    }

    /**
     * Return the name that should be used to save the file
     * @return string
     */
    protected function returnNewFileName()
    {
        $fileName = $this->returnShortName();
        $randomName = $fileName."_".time().mt_rand(10, 99);
        return $randomName;
    }

    /**
     * Return folder name where the image should be saved
     * @return string
     */
    protected function returnFolderName()
    {
        return $this->returnShortName();
    }

    /**
     * Return owner class short name
     * @return string
     */
    protected function returnShortName()
    {
        $owner = $this->owner;
        $class = new \ReflectionClass($owner);
        $name = Inflector::camel2id($class->getShortName());
        return $name;
    }

    /**
     * Return the file link
     * @param string $attribute
     * @return string
     */
    public function returnFileLink($attribute = null)
    {
        $owner = $this->owner;
        $folderName = $this->returnFolderName();
        if(!$attribute) {
            $attribute = $this->attribute;
        }
        $fileName = $owner->getAttribute($attribute);
        $return = null;
        if($fileName) {
            $return = Yii::getAlias('@web/uploads/'.$folderName.'/'.$fileName);
        }
        return $return;
    }

}
