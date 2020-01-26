<?php

namespace webmenedzser\reporter\jobs;

use webmenedzser\reporter\services\CallBackend;

use Craft;
use craft\queue\BaseJob;

class CallBackendJob extends BaseJob
{
    public function execute($queue)
    {
        new CallBackend();
    }

    protected function defaultDescription()
    {
        return 'Requesting Craft Report to check the site.';
    }
}
