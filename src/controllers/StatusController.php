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

use webmenedzser\reporter\Reporter;
use webmenedzser\reporter\services\Versions;

use Craft;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

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
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Function that gets hit when a request is made to `/reporter/status`.
     *
     * @return array|false|string
     * @throws BadRequestHttpException
     * @throws UnauthorizedHttpException
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
