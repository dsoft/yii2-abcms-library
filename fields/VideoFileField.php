<?php

namespace abcms\library\fields;

/**
 * Video File Field
 */
class VideoFileField extends FileField
{

    /**
     * @inherit
     */
    public $folder = 'uploads/files/videos/';
    
    /**
     * @inherit
     */
    public $extensions = ['mp4'];

    /**
     * @inherit
     */
    protected function returnFileName()
    {
        return 'video';
    }

}
