<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\assetbundles\utilities;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Asset bundle for Restore Utility
 */
class RestoreUtilityAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/dist';

    /**
     * @inheritdoc
     */
    public $depends = [
        CpAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/app.js',
    ];
}
