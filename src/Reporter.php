<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter;

use webmenedzser\reporter\jobs\CallBackendJob;
use webmenedzser\reporter\models\Settings;
use webmenedzser\reporter\utilities\RestoreUtility;

use Craft;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;
use craft\log\MonologTarget;
use craft\services\Plugins;
use craft\services\ProjectConfig;
use craft\services\UserPermissions;
use craft\services\Utilities;
use craft\web\UrlManager;

use yii\base\Event;
use yii\base\Exception;
use yii\log\Logger;

use Monolog\Formatter\LineFormatter;
use Psr\Log\LogLevel;

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
    public string $schemaVersion = '1.2.0';

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public bool $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'webmenedzser\reporter\console\controllers';
        }

        $this->_registerLogTarget();
        $this->_afterInstall();
        $this->_registerEvents();
        $this->_registerPermissions();
        $this->_registerUtilities();
    }

    // Protected Methods
    // =========================================================================
    /**
     * @inheritdoc
     */
    protected function createSettingsModel() : ?\craft\base\Model
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml() : ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'craft-reporter/_settings',
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
    private function _afterInstall() : void
    {
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this && !Craft::$app->getRequest()->isConsoleRequest ) {
                    Craft::$app->response->redirect(UrlHelper::cpUrl('settings/plugins/craft-reporter'))->send();
                }
            }
        );
    }

    /**
     * Register event listeners.
     */
    private function _registerEvents() : void
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            static function (RegisterUrlRulesEvent $event) {
                $event->rules['reporter/status'] = 'craft-reporter/status/index';
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_DISABLE_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin !== $this) {
                    $this->_callBackend();
                }
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_ENABLE_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin !== $this) {
                    $this->_callBackend();
                }
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin !== $this) {
                    $this->_callBackend();
                }
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_UNINSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin !== $this) {
                    $this->_callBackend();
                }
            }
        );

        Event::on(
            ProjectConfig::class,
            ProjectConfig::EVENT_AFTER_APPLY_CHANGES,
            function(Event $event) {
                $this->_callBackend();
            }
        );

        if (defined('craft\services\ProjectConfig::EVENT_REBUILD')) {
            Event::on(
                ProjectConfig::class,
                ProjectConfig::EVENT_REBUILD,
                function(Event $event) {
                    $this->_callBackend();
                }
            );
        }
    }

    private function _registerPermissions() : void
    {
        // If Craft edition is pro
        if (Craft::$app->getEdition() === Craft::Pro) {
            Event::on(
                UserPermissions::class,
                UserPermissions::EVENT_REGISTER_PERMISSIONS,
                static function(RegisterUserPermissionsEvent $event) {
                    $event->permissions[] = [
                        'heading' => 'Craft Reporter',
                        'permissions' => [
                            'craft-reporter:restore-utility' => [
                                'label' => Craft::t(
                                    'craft-reporter',
                                    'Restore Backups'
                                )
                            ]
                        ]
                    ];
                }
            );
        }
    }

    private function _registerUtilities() : void
    {
        // Register utility
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function(RegisterComponentTypesEvent $event) {
                if (Craft::$app->getUser()->checkPermission('craft-reporter:restore-utility')) {
                    $event->types[] = RestoreUtility::class;
                }
            }
        );
    }

    /**
     * Logs a message.
     *
     * @param string $message
     * @param int    $type
     *
     * @return void
     */
    public function log(string $message, int $type = Logger::LEVEL_INFO) : void
    {
        Craft::getLogger()->log($message, $type, 'craft-reporter');
    }

    private function _registerLogTarget() : void
    {
        Craft::getLogger()->dispatcher->targets[] = new MonologTarget([
            'name' => 'craft-reporter',
            'categories' => ['craft-reporter'],
            'level' => LogLevel::INFO,
            'logContext' => false,
            'allowLineBreaks' => false,
            'formatter' => new LineFormatter(
                format: "[%datetime%] %message%\n",
                dateFormat: 'Y-m-d H:i:s',
            ),
        ]);
    }

    private function _callBackend() : void
    {
        Craft::$app->queue->push(new CallBackendJob());
    }

    /**
     * Generates a new API Key.
     *
     * @return string
     * @throws Exception
     */
    private function _generateApiKey() : string
    {
        return Craft::$app->security->generateRandomString(30);
    }
}
