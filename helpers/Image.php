<?php

namespace abcms\library\helpers;

use Yii;
use yii\imagine\BaseImage;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use yii\helpers\StringHelper;
use yii\helpers\FileHelper;
use yii\base\ErrorException;

/**
 * Image implements most commonly used image manipulation functions using the [Imagine library](http://imagine.readthedocs.org/).
 */
class Image extends BaseImage
{

    /**
     * Creates a thumbnail image. The function differs from `\Yii\Imagine\Image::thumbnail()` function that
     * it creates a thumbnail with the exact size event if the image provided is smaller
     * @param string $filename the image file path or path alias.
     * @param integer $width the width in pixels to create the thumbnail
     * @param integer $height the height in pixels to create the thumbnail
     * @param string $mode
     * @return ImageInterface
     */
    public static function thumbnail($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND)
    {
        $box = new Box($width, $height);
        $img = static::getImagine()->open(Yii::getAlias($filename));

        if(!$box->getWidth() && !$box->getHeight()) {
            return $img->copy();
        }

        $imageSize = $img->getSize();
        if($imageSize->getWidth() <= $box->getWidth() && $imageSize->getHeight() <= $box->getHeight()) {
            $ratios = array(
                $box->getWidth() / $imageSize->getWidth(),
                $box->getHeight() / $imageSize->getHeight()
            );
            $ratio = max($ratios);
            $imageSize = $imageSize->scale($ratio);
            $img->resize($imageSize);
        }

        $img = $img->thumbnail($box, $mode);

        $palette = new \Imagine\Image\Palette\RGB();
        $color = $palette->color(static::$thumbnailBackgroundColor, 0);
        
        // create empty image to preserve aspect ratio of thumbnail
        $thumb = static::getImagine()->create($box, $color);

        // calculate points
        $size = $img->getSize();

        $startX = 0;
        $startY = 0;
        if($size->getWidth() < $width) {
            $startX = ceil($width - $size->getWidth()) / 2;
        }
        if($size->getHeight() < $height) {
            $startY = ceil($height - $size->getHeight()) / 2;
        }

        $thumb->paste($img, new Point($startX, $startY));

        return $thumb;
    }

    /**
     * Resize the image
     * @param string $filename the image file path or path alias.
     * @param integer $width the width in pixels to create the thumbnail
     * @param integer $height the height in pixels to create the thumbnail
     * @return ImageInterface
     */
    public static function resize($filename, $width, $height = null, $keepAspectRatio = true, $allowUpscaling = false)
    {
        $img = static::getImagine()->open(Yii::getAlias($filename));


        if(!$width && !$height) {
            return $img->copy();
        }
        $originalSize = $img->getSize();
        if($width && !$height) {
            $height = $width * $originalSize->getHeight() / $originalSize->getWidth();
        }
        if($height && !$width) {
            $width = $height * $originalSize->getWidth() / $originalSize->getHeight();
        }
        return $img->copy()->resize(new Box($width, $height));
    }

    /**
     * Take one image path and save multiple sizes for it, create directories of new sizes if necessary
     * @param string $folderPath
     * @param string $imageName
     * @param array $sizes The sizes of the image that should be saved, array should contain the name of the size as key, used also as folder name,
     * and an array as value containing width, height or both.
     * @throws ErrorException
     */
    public static function saveSizes($folderPath, $imageName, $sizes)
    {
        $imagePath = $folderPath.$imageName;
        /**
         * @var array Image saving options
         */
        $options = array();
        if(StringHelper::endsWith($imageName, 'jpg', false) || StringHelper::endsWith($imageName, 'jpeg', false)) {
            // Keep good quality if image is jpeg
            $options = array('quality' => 95);
        }
        foreach($sizes as $name => $size) {
            if(isset($size['width']) || isset($size['height'])) {
                $folderName = $folderPath.$name.'/';
                $newImagePath = $folderName.$imageName;
                if(FileHelper::createDirectory($folderName)) {
                    $width = (isset($size['width'])) ? $size['width'] : 0;
                    $height = (isset($size['height'])) ? $size['height'] : 0;
                    if(!$width || !$height) {
                        Image::resize($imagePath, $width, $height)->save($newImagePath, $options);
                    }
                    else {
                        Image::thumbnail($imagePath, $width, $height)->save($newImagePath, $options);
                    }
                }
                else {
                    throw new ErrorException('Unable to create directoy.');
                }
            }
        }
    }

}
