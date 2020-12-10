<?php
/**
 * Reporter plugin for Craft CMS 3.x
 *
 * Reporter plugin for Craft CMS.
 *
 * @link      https://www.webmenedzser.hu
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\console\controllers;

use webmenedzser\reporter\services\BackupService;

use Craft;

use yii\console\Controller;

/**
 * Craft Report CLI commands.
 *
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.11.0
 */
class BackupController extends Controller
{
    /**
     * Restore remote backup from Craft Report.
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionRestore()
    {
        $backupService = new BackupService();

        return $backupService->restoreDbBackup();
    }
}
