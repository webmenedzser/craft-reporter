<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\console\controllers;

use webmenedzser\reporter\services\BackupService;

use Craft;

use yii\console\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

use Exception;

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
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException|Exception
     */
    public function actionRestore()
    {
        $backupService = new BackupService();

        return $backupService->restoreDbBackup();
    }
}
