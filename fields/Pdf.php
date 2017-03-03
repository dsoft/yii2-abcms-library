<?php

namespace abcms\library\fields;

/**
 * PDF File Field
 */
class Pdf extends File
{

    /**
     * @inherit
     */
    public $folder = 'uploads/files/pdfs/';
    
    /**
     * @inherit
     */
    public $extensions = ['pdf'];

    /**
     * @inherit
     */
    protected function returnFileName()
    {
        return 'pdf';
    }

}
