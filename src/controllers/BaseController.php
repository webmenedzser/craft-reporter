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
use yii\web\NotFoundHttpException;
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
     * @throws NotFoundHttpException
     */
    protected function checkIfAuthenticated() : void
    {
        $path = Craft::$app->request->getFullPath();
        $key = Craft::$app->request->getParam('key');
        $apiKey = Craft::parseEnv(Reporter::$plugin->getSettings()->apiKey);

        if (!Craft::$app->request->isPost) {
            throw new NotFoundHttpException('Template not found: ' . $path);
        }

        if (!$key) {
            throw new NotFoundHttpException('Template not found: ' . $path);
        }

        if ($key !== $apiKey) {
            throw new NotFoundHttpException('Template not found: ' . $path);
        }
    }
}
