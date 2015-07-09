<?php

namespace abcms\library\helpers;

class Inflector extends \yii\helpers\BaseInflector
{

    /**
     * Keep arabic characters
     * @inheritdoc
     */
    public static function slug($string, $replacement = '-', $lowercase = true)
    {
        $string = strip_tags($string);
        $string = preg_replace('/[=\s—–-]+/u', $replacement, $string);
        $string = preg_replace('/[^\x{0600}-\x{06FF}A-Za-z0-9\-]/u', '', $string);
        $string = rtrim($string, '-');
        if($lowercase && !preg_match('/\p{Arabic}/u', $string)) {
            $string = strtolower($string);
        }
        return $string;
    }

}
