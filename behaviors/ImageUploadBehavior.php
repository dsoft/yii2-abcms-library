<?php

namespace abcms\library\behaviors;

use Yii;
use yii\helpers\FileHelper;
use yii\base\ErrorException;
use abcms\library\helpers\Image;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * ImageUploadBehavior automatically validate, upload and resize the available image for a certain attribute.
 *
 * To use it, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use abcms\library\behaviors\ImageUploadBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *          [
 *              'class' => ImageUploadBehavior::className(),
 *              'attribute' => 'image',
 *              'sizes' => [
 *                  'small' => [
 *                      'width' => 340,
 *                      'height' => 300,
 *                  ]
 *              ],
 *          ],
 *     ];
 * }
 * ```
 */
class ImageUploadBehavior extends FileUploadBehavior
{

    /**
     * Additional images sizes.
     * ```php
     * 'sizes' => [
     *              'small' => [
     *                  'width' => 200,
     *                  'height' => 300,
     *              ],
     *              'medium' => [
     *                  'width' => 600,
     *              ],
     *          ],
     * ```
     * @var array
     */
    public $sizes = [];
    
    /**
     * @inheritdoc
     */
    public $extensions = 'png, jpg';
    
    /**
     * @inheritdoc
     */
    protected $validatorType = 'image';
    
    /**
     * @inheritdoc
     * Image required on create scenario only.
     * This is the most used case in the admin panel.
     */
    public $requiredOn = 'create';

    /**
     * @inheritdoc
     */
    protected function afterFileSave($directory, $fileName){
        $this->saveSizes($directory, $fileName);
    }
    
    /**
     * Save additional images sizes.
     * @param string $mainFolder
     * @param string $imageName
     */
    protected function saveSizes($mainFolder, $imageName)
    {
        $options = array();
        if(StringHelper::endsWith($imageName, 'jpg', false) || StringHelper::endsWith($imageName, 'jpeg', false)) {
            // Keep good quality if image is jpeg
            $options = array('quality' => 95);
        }
        $sizes = (array) $this->sizes;
        foreach($sizes as $name => $size) {
            if(isset($size['width']) || isset($size['height'])) {
                $folderName = $mainFolder.$name.'/';
                if(FileHelper::createDirectory($folderName)) {
                    $width = (isset($size['width'])) ? $size['width'] : 0;
                    $height = (isset($size['height'])) ? $size['height'] : 0;
                    if(!$width || !$height) {
                        Image::resize($mainFolder.$imageName, $width, $height)->save($folderName.$imageName, $options);
                    }
                    else {
                        Image::thumbnail($mainFolder.$imageName, $width, $height)->save($folderName.$imageName, $options);
                    }
                }
                else {
                    throw new ErrorException('Unable to create directoy.');
                }
            }
        }
    }

    /**
     * Returns the image link.
     * @param string $attribute
     * @param string $size
     * @param bool|string $scheme the URI scheme to use in the returned base URL
     * @return string
     */
    public function returnImageLink($attribute = null, $size = null, $scheme = false)
    {
        $owner = $this->owner;
        $folderName = $this->returnFolderName();
        if ($size) {
            $folderName .= "/$size";
        }
        if (!$attribute) {
            $attribute = $this->attribute;
        }
        $imageName = $owner->getAttribute($attribute);
        $return = null;
        if ($imageName) {
            $return = Url::base($scheme) . '/uploads/' . $folderName . '/' . $imageName;
        }
        return $return;
    }

}
