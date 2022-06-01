<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\helpers;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.10.0
 */
class FilenameHelper
{
    public static function getFilename($prefix, $slug, $extension)
    {
        return $prefix . '-' . $slug . '-' . date('Ymd-His') . '.' . $extension;
    }
}
