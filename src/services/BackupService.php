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

use ZipArchive;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.9.0
 */
class BackupService extends Component
{
    public function createDbBackup() : string
    {
        $backupPath = Craft::$app->db->backup();

        /**
         * Get filename of the backup.
         */
        $filepathSegments = explode(DIRECTORY_SEPARATOR, $backupPath);
        $localPath = $filepathSegments[count($filepathSegments) - 1];

        /**
         * Create a zip archive.
         */
        $zip = new ZipArchive();
        $zipPath = $backupPath . '.zip';
        if ($zip->open($zipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
            die ("An error occurred when creating ZIP file.");
        }

        /**
         * Add file from $backupPath to the $localPath path in the archive.
         */
        $zip->addFile($backupPath, $localPath);

        /**
         * If there is an encryption key set, set a password for the file in the archive.
         */
        $backupEncryptionKey = Craft::parseEnv(Reporter::getInstance()->getSettings()->backupEncryptionKey);
        if ($backupEncryptionKey) {
            $zip->setEncryptionIndex(0, ZipArchive::EM_AES_256, $backupEncryptionKey);
        }
        $zip->close();

        /**
         * Delete the uncompressed backup.
         */
        unlink($backupPath);

        return $zipPath;
    }
}
