<?php
/**
 * Reporter plugin for Craft CMS 3.x
 *
 * Reporter plugin for Craft CMS.
 *
 * @link      https://www.webmenedzser.hu
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter;

use webmenedzser\reporter\models\Settings;
use webmenedzser\reporter\services\Recheck;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\services\ProjectConfig;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\log\FileTarget;
use craft\helpers\UrlHelper;

use yii\base\Event;

/**
 * Class Reporter
 *
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.0.0
 */
class Reporter extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Reporter
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->_registerLogger();
        $this->_redirectAfterInstall();
        $this->_registerEvents();
    }

    // Protected Methods
    // =========================================================================
    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            'reporter/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }

    // Private Methods
    // =========================================================================

    /**
     * Redirect user to the plugin settings page after install.
     */
    private function _redirectAfterInstall()
    {
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this && !Craft::$app->getRequest()->isConsoleRequest ) {
                    Craft::$app->response->redirect(UrlHelper::cpUrl('settings/plugins/reporter'))->send();
                }
            }
        );
    }

    /**
     * Register event listeners.
     */
    private function _registerEvents()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['reporter/status'] = 'reporter/status/index';
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_BEFORE_SAVE_PLUGIN_SETTINGS,
            function (PluginEvent $event) {
                $settings = Craft::$app->request->getParam('settings');

                if ($event->plugin === $this && isset($settings['regenerate'])) {
                    $user = Craft::$app->getUser()->getIdentity();
                    $newKey = $this->generateApiToken();

                    Craft::$app->session->setNotice(Craft::t('reporter', 'Generated a new API Key. Make sure to save your settings.'));
                    Craft::$app->session->setFlash('apiKey', $newKey);

                    return Craft::$app->response->redirect(Craft::$app->request->getUrl())->sendAndClose();
                }
            }
        );
    }

    private function _registerLogger()
    {
        // Create a new file target
        $fileTarget = new FileTarget([
            'logFile' => '@storage/logs/reporter.log',
            'categories' => ['webmenedzser\reporter\*']
        ]);

        // Add the new target file target to the dispatcher
        Craft::getLogger()->dispatcher->targets[] = $fileTarget;

    }

    /**
     * Generates a new API token.
     *
     * @return string
     */
    private function generateApiToken(): string
    {
        return strtolower(static::key(40));
    }

    /**
     * Generates a new license key.
     *
     * @param int $length
     * @param string $extraChars
     * @return string
     */
    private function key(int $length, string $extraChars = ''): string
    {
        $licenseKey = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.$extraChars;
        $alphabetLength = strlen($codeAlphabet);
        $log = log($alphabetLength, 2);
        $bytes = (int)($log / 8) + 1; // length in bytes
        $bits = (int)$log + 1; // length in bits
        $filter = (int)(1 << $bits) - 1; // set all lower bits to 1

        for ($i = 0; $i < $length; $i++) {
            do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd &= $filter; // discard irrelevant bits
            } while ($rnd >= $alphabetLength);

            $licenseKey .= $codeAlphabet[$rnd];
        }

        return $licenseKey;
    }
}
