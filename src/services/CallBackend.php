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
use craft\base\Component;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.0.0
 */
class CallBackend extends Component
{
    // TODO: change this URL before release!
    public $backendUrl = 'https://app.craft.report/api/v1/report/status';
    public $siteUrl;
    public $apiKey;
    public $client;
    public $response;

    private function _initiate()
    {
        $this->siteUrl = Reporter::$plugin->getSettings()->siteUrl;
        $this->apiKey = Reporter::$plugin->getSettings()->apiKey;
        $this->client = new Client();
    }

    public function connect()
    {
        $this->_initiate();

        try {
            $this->response = $this->client->request(
                'POST',
                $this->backendUrl,
                [
                    'form_params' => [
                        'key' => $this->apiKey,
                        'siteUrl' => $this->siteUrl
                    ]
                ]
            );

            Craft::info(
                print_r($this->response, true),
                __METHOD__
            );

            return false;
        } catch (ClientException $e) {
            return $e;
        }
    }
}
