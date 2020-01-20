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

use Craft;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.0.0
 */
class BaseController extends Controller
{
    /**
     * Checks if the request should be fulfilled or not.
     *
     * @throws BadRequestHttpException
     * @throws UnauthorizedHttpException
     */
    protected function checkIfAuthenticated()
    {
        if (!Craft::$app->request->isPost) {
            $message = 'Only POST requests are supported.';

            throw new BadRequestHttpException($message);
        }

        $key = Craft::$app->request->getParam('key');
        $apiKey = Reporter::$plugin->getSettings()->apiKey;

        if (!$key) {
            $message = 'Missing parameter: `key`.';

            throw new BadRequestHttpException($message);
        }

        if ($key !== $apiKey) {
            $message = 'Unauthenticated access is not allowed.';

            throw new UnauthorizedHttpException($message);
        }
    }
}
