<?php

namespace abcms\library\fields;

use yii\helpers\Html;
use yii\validators\FileValidator;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use Yii;

/**
 * Text Input Field
 */
class File extends Field
{

    /**
     * @var array allowed extensions
     */
    public $extensions = [];

    /**
     * Folder Path where file should be saved
     * @var string
     */
    public $folder = 'uploads/files/';

    /**
     * Renders field input
     */
    public function renderInput()
    {
        $html = Html::fileInput($this->inputName, $this->value, $this->inputOptions);
        $html .= Html::hiddenInput($this->inputName, '', ['id' => null]);
        if($this->value) {
            $html .= Html::a($this->value, $this->getFileLink(), ['target' => '_blank']);
            $html .= '<br />';
        }
        return $html;
    }
    
    /**
     * {@inheritdoc}
     */
    public function renderActiveField($activeField)
    {
        $activeField = parent::renderActiveField($activeField);
        $field = $activeField->fileInput();
        return $field;
    }

    /**
     * @inherit
     */
    public function validate()
    {
        $validator = new FileValidator;
        $validator->extensions = $this->extensions;
        $file = UploadedFile::getInstanceByName($this->inputName);

        if($validator->validate($file)) {
            if($file) {
                $fileName = $this->returnFileName();
                $randomName = $fileName."_".time().mt_rand(10, 99).".".$file->extension;
                $directory = Yii::getAlias('@webroot/'.$this->folder);
                if(FileHelper::createDirectory($directory)) {
                    $mainFilePath = $directory.$randomName;
                    $file->saveAs($mainFilePath);
                    $this->value = $randomName;
                    $this->afterFileSave();
                    return true;
                }
                else {
                    throw new ErrorException('Unable to create directoy.');
                }
            }
        }
        return false;
    }

    /**
     * Override to add functionalities after saving the file
     */
    protected function afterFileSave()
    {
        
    }

    /**
     * File name prefix, should be used to save the file
     * @return string
     */
    protected function returnFileName()
    {
        return 'file';
    }

    /**
     * Return file link
     * @return string|null
     */
    public function getFileLink()
    {
        $fileName = $this->value;
        $return = null;
        if($fileName) {
            $return = Yii::getAlias('@web/'.$this->folder.$fileName);
        }
        return $return;
    }

    /**
     * @inherit
     */
    public function getDetailViewAttribute()
    {
        $link = Html::encode($this->getFileLink());
        if(!$link) {
            return [
                'label' => $this->label,
                'value' => NULL,
            ];
        }
        $array = [
            'label' => $this->label,
            'value' => '<a href="'.$link.'" target="_blank">'.$link.'</a>',
            'format' => ['raw'],
        ];
        return $array;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addRulesToModel($model)
    {
        $oldValue = $model->{$this->inputName};
        $model->attachBehavior('file_'.$this->inputName, [
                'class' => \abcms\library\behaviors\FileUploadBehavior::className(),
                'attribute' => $this->inputName,
                'extensions' => $this->extensions,
                'folderName' => $this->folder,
                'required' => ($this->isRequired && !$oldValue),
                'oldValue' => $oldValue,
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function isSafe()
    {
        return false;
    }

}
