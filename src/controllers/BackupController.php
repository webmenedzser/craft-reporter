<?php
/**
 * Reporter plugin for Craft CMS 3.x
 *
 * Reporter plugin for Craft CMS.
 *
 * @link      https://www.webmenedzser.hu
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\controllers;

use webmenedzser\reporter\services\BackupService;

use Craft;

use yii\web\NotFoundHttpException;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.9.0
 */
class BackupController extends BaseController
{
    // Protected Properties
    // =========================================================================

    /**
     * Disable CSRF validation for the entire controller
     *
     * @var bool
     */
    public $enableCsrfValidation = false;

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Function that gets hit when a request is made to `/reporter/backup`.
     *
     * @throws NotFoundHttpException
     * @throws \craft\errors\ShellCommandException
     * @throws \yii\base\Exception
     */
    public function actionIndex()
    {
        /**
         * Check if the request has the proper API keys, deny access if not.
         */
        $this->checkIfAuthenticated();

        $backupService = new BackupService();
        $backupService->createDbBackup();
    }

    /**
     *
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionRestore()
    {
        $this->requirePostRequest();
        $this->requireCpRequest();
        $this->requirePermission('craft-reporter:restore-utility');

        $backupService = new BackupService();

        return $backupService->restoreDbBackup();
    }
}
