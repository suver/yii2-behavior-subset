<?php

namespace suver\behavior\subset\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
	public $sourcePath = '@vendor/suver/behavior/subset/assets';
//	public $basePath = '@webroot';
//    public $baseUrl = '@web';
    public $css = [
        'chosen_v1.8.7/chosen.css',
//        'css/style.css',
    ];
    public $js = [
        'chosen_v1.8.7/chosen.jquery.js',
//        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
}
