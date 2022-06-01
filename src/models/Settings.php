<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

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
    public function behaviors() : array
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
