<?php
/**
 * Craft Report companion plugin for Craft CMS 4.x
 *
 * @link      https://craft.report
 * @copyright Copyright (c) 2020 Ottó Radics
 */

namespace webmenedzser\reporter\services;

use webmenedzser\reporter\Reporter;
use webmenedzser\reporter\helpers\FilenameHelper;

use Craft;
use craft\base\Component;
use craft\errors\ShellCommandException;

use \Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use ZipArchive;

/**
 * @author    Ottó Radics
 * @package   Reporter
 * @since     1.9.0
 */
class BackupService extends Component
{
    /**
     * @throws ShellCommandException
     * @throws \yii\base\Exception
     */
    public function createDbBackup() : void
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
        if ($zip->open($zipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== true) {
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
         * Stream file to client.
         */
        Craft::$app->getResponse()->sendFile($zipPath);

        /**
         * Delete the files to prevent unnecessary storage use.
         */
        unlink($backupPath);
        unlink($zipPath);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function restoreDbBackup()
    {
        $location = Craft::$app->getPath()->getDbBackupPath();
        $zipPath = $this->_downloadBackup($location);
        $backupFilename = $this->_extractBackup($zipPath);
        $backupPath = $location . DIRECTORY_SEPARATOR . $backupFilename;

        if (!is_file($backupPath)) {
            throw new FileNotFoundException();
        }

        try {
            Craft::$app->getDb()->restore($backupPath);
        } catch (\Throwable $e) {
            Craft::$app->getErrorHandler()->logException($e);

            return false;
        }

        unlink($backupPath);

        return true;
    }

    /**
     * Download DB Backup from Craft Report.
     *
     * @param String $location
     *
     * @return false|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function _downloadBackup(String $location)
    {
        $key = Craft::parseEnv(Reporter::$plugin->getSettings()->apiKey);
        if (!$key) {
            throw new \Exception('Craft Report API Key is not set.');
        }

        $client = new Client();
        $filename = FilenameHelper::getFilename('db-backup', $key, 'zip');
        $path = $location . DIRECTORY_SEPARATOR . $filename;

        $client->request(
            'POST',
            'https://craft.report/api/v1/restore',
            [
                'sink' => $path,
                'form_params' => [
                    'key' => $key
                ]
            ]
        );


        return $path;
    }

    /**
     * Extract archive on $path.
     *
     * @param String $zipPath
     *
     * @return false|string
     * @throws \yii\base\Exception
     */
    private function _extractBackup(String $zipPath) : String
    {
        /**
         * Open and extract archive.
         */
        $zipArchive = new ZipArchive();
        if ($zipArchive->open($zipPath) !== true) {
            throw new Exception('Failed extracting ZIP archive.');
        }

        /**
         * Set backupEncryptionKey if it is set in plugin settings.
         */
        $backupEncryptionKey = Craft::parseEnv(Reporter::$plugin->getSettings()->backupEncryptionKey) ?? null;
        if ($backupEncryptionKey) {

            $zipArchive->setPassword($backupEncryptionKey);
        }

        $backupFilename = $zipArchive->getNameIndex(0);

        $zipArchive->extractTo(Craft::$app->getPath()->getDbBackupPath());
        $zipArchive->close();

        /**
         * Delete archive.
         */
        unlink($zipPath);

        return $backupFilename;
    }
}
