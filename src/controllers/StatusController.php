<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\controllers;

use webmenedzser\reporter\services\Versions;

use yii\web\NotFoundHttpException;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.0.0
 */
class StatusController extends BaseController
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
    protected array|int|bool $allowAnonymous = ['index'];

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

        /**
         * Get and return the response of Versions service.
         */
        $versions = new Versions();

        return $versions->all();
    }
}
