<?php
namespace abcms\library\grid;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the javascript files for the [[InlineFormGridView]] widget.
 */
class InlineFormGridViewAsset extends AssetBundle
{
    public $sourcePath = '@abcms/library/grid/assets';
    public $js = [
        'abcms.inlineFormGridView.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
