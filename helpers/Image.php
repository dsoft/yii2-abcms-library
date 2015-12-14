<?php

namespace abcms\library\helpers;

use Yii;
use yii\imagine\BaseImage;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;

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

        // create empty image to preserve aspect ratio of thumbnail
        $thumb = static::getImagine()->create($box, new Color('FFF', 100));

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
    public static function resize($filename, $width, $height=null)
    {
        $img = static::getImagine()->open(Yii::getAlias($filename));
        

        if(!$width && !$height) {
            return $img->copy();
        }
        $originalSize = $img->getSize();
        if($width && !$height){
            $height = $width * $originalSize->getHeight() / $originalSize->getWidth();
        }
        if($height && !$width){
            $width = $height * $originalSize->getWidth() / $originalSize->getHeight();
        }
        return $img->copy()->resize(new Box($width, $height));
    }

}
