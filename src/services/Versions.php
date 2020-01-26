<?php
/**
 * Reporter plugin for Craft CMS 3.x
 *
 * Reporter plugin for Craft CMS.
 *
 * @link      https://www.webmenedzser.hu
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\services;

use webmenedzser\reporter\Reporter;

use Craft;
use Yii;
use craft\base\Component;
use craft\helpers\App;
use GuzzleHttp\Client;
use Imagine\Gd\Imagine;
use Twig\Environment;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.0.0
 */
class Versions extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function all()
    {
        $result = [
            'updates' => $this->_updates(),
            'plugins' => $this->_plugins(),
            'core' => $this->_core(),
            'runtime' => $this->_runtime(),
        ];

        $result = json_encode($result);

        return $result;
    }

    /**
     * Function to return an array with information about the currently available plugins.
     *
     * @return array
     */
    private function _plugins()
    {
        $plugins = Craft::$app->plugins->getAllPluginInfo();
        $array = [];

        foreach ($plugins as $plugin) {
            $array[] = $plugin;
        }

        return $array;
    }

    /**
     * Function to return an array with information about the core.
     *
     * @return array
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    private function _core()
    {
        return [
            'edition' => Craft::$app->getEditionName(),
            'licencedEdition' => Craft::$app->getLicensedEditionName(),
            'info' => Craft::$app->getInfo(),
            'devMode' => Craft::$app->config->general->devMode
        ];
    }

    private function _runtime()
    {
        return [
            'phpVersion' => App::phpVersion(),
            'osVersion' => PHP_OS . ' ' . php_uname('r'),
            'yiiVersion' => Yii::getVersion(),
            'twigVersion' => Environment::VERSION,
            'imagineVersion' => Imagine::VERSION,
        ];
    }

    /**
     * Get available updates for installed components.
     *
     * @return \craft\models\Updates
     */
    private function _updates()
    {
        return Craft::$app->updates->getUpdates(true);
    }
}
