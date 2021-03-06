<?php

namespace webmenedzser\reporter\models;

use Craft;
use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

class Settings extends Model
{
    public $apiKey = '';
    public $exposeComposerLock = false;
    public $backupEncryptionKey = '';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['parser'] = [
            'class' => EnvAttributeParserBehavior::class,
            'attributes' => [
                'apiKey',
                'backupEncryptionKey'
            ],
        ];

        return $behaviors;
    }
}
