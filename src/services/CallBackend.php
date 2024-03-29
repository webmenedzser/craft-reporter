<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\services;

use webmenedzser\reporter\Reporter;

use Craft;
use craft\base\Component;
use craft\behaviors\EnvAttributeParserBehavior;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.0.0
 */
class CallBackend extends Component
{
    public $backendUrl = 'https://craft.report/api/v1/report/status';
    public $apiKey;
    public $client;
    public $response;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->apiKey = Craft::parseEnv(Reporter::$plugin->getSettings()->apiKey);
        $this->client = new Client();

        $this->_connect();
    }

    /**
     * @return bool|\Exception|ClientException
     */
    private function _connect()
    {
        try {
            $this->response = $this->client->request(
                'POST',
                $this->backendUrl,
                [
                    'form_params' => [
                        'key' => $this->apiKey,
                    ]
                ]
            );

            return false;
        } catch (ClientException $e) {
            return $e;
        }
    }
}
