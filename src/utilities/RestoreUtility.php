<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\utilities;

use webmenedzser\reporter\assetbundles\utilities\RestoreUtilityAsset;
use webmenedzser\reporter\Reporter;

use Craft;
use craft\base\Utility;

/**
 * Class RestoreUtility
 *
 * @package webmenedzser\reporter\utilities
 * @since: 1.10.0
 */
class RestoreUtility extends Utility
{
    public static function displayName() : string
    {
        return Craft::t('craft-reporter', 'Restore Database Backup');
    }

    public static function id() : string
    {
        return 'craft-reporter-restore-utility';
    }

    public static function iconPath()
    {
        return Craft::getAlias('@vendor/webmenedzser/craft-reporter/src/icon-mask.svg');
    }

    public static function contentHtml() : string
    {
        $view = Craft::$app->getView();

        return Craft::$app->getView()->renderTemplate('craft-reporter/_restore-utility', [
            'settings' => Reporter::$plugin->getSettings(),
        ]);
    }
}
