<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\jobs;

use webmenedzser\reporter\services\CallBackend;

use Craft;
use craft\queue\BaseJob;

class CallBackendJob extends BaseJob
{
    public function execute($queue) : void
    {
        new CallBackend();
    }

    protected function defaultDescription() : ?string
    {
        return 'Requesting Craft Report to check the site.';
    }
}
