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
     * Function that gets hit when a request is made to `/reporter/status`.
     *
     * @return array|false|string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        /**
         * Check if the request has the proper API keys, deny access if not.
         */
        $this->checkIfAuthenticated();

        $backupService = new BackupService();
        $backupFile = $backupService->createDbBackup();

        return Craft::$app->getResponse()->sendFile($backupFile);
    }
}
