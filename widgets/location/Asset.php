<?php

namespace abcms\library\widgets\location;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@abcms/library/widgets/location';
    public $css = [
    ];
    public $js = [
        'https://maps.googleapis.com/maps/api/js',
        'script.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
