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
            'composerLock' => null
        ];

        if (Reporter::$plugin->getSettings()->exposeComposerLock) {
            $result['composerLock'] = $this->_composerLock();
        }

        $result = json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

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
        $core = [
            'edition' => Craft::$app->getEditionName(),
            'licencedEdition' => Craft::$app->getLicensedEditionName(),
            'info' => $this->_cleanInfo(),
            'devMode' => Craft::$app->config->general->devMode
        ];

        return $core;
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

    /**
     * Expose composer.lock content
     *
     * @since 1.7.0
     * @return mixed|null
     */
    private function _composerLock()
    {
        $dependencies = [];
        $composerLockPath = Craft::getAlias(CRAFT_BASE_PATH . DIRECTORY_SEPARATOR . 'composer.lock');

        try {
            $composerLockContent = file_get_contents($composerLockPath);

            return json_decode($composerLockContent);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Safely unset unneeded members of $info.
     *
     * @throws \yii\web\ServerErrorHttpException
     */
    private function _cleanInfo()
    {
        $info = Craft::$app->getInfo();

        $config = $info['config'] ?? null;
        $configMap = $info['configMap'] ?? null;

        if ($config) {
            unset($info['config']);
        }

        if ($configMap) {
            unset($info['configMap']);
        }

        return $info;
    }
}
