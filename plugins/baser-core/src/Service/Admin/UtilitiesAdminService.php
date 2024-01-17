<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service\Admin;

use BaserCore\Service\UtilitiesService;
use BaserCore\Utility\BcSiteConfig;
use Cake\Core\Configure;
use Cake\Database\Driver\Mysql;
use Cake\ORM\Locator\TableLocator;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * UtilitiesAdminService
 */
class UtilitiesAdminService extends UtilitiesService implements UtilitiesAdminServiceInterface
{

    /**
     * info 画面用の view 変数を生成
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForInfo(): array
    {
        $db = TableRegistry::getTableLocator()->get('BaserCore.App')->getConnection();
        $driver = $db->config()['driver'];
        $sqlMode = '';
        if($driver === Mysql::class) {
            $sqlMode = $db->execute('SELECT @@global.sql_mode;')->fetch()[0];
        }
        return [
            'datasource' => $this->_getDriver(),
            'baserVersion' => BcSiteConfig::get('version'),
            'cakeVersion' => Configure::version(),
            'sqlMode' => $sqlMode
        ];
    }

    /**
     * データベースのドライバー情報を取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    private function _getDriver(): string
    {
        $drivers = ['csv' => 'CSV', 'sqlite' => 'SQLite', 'mysql' => 'MySQL', 'postgres' => 'PostgreSQL'];
        $config = TableRegistry::getTableLocator()->get('BaserCore.App')->getConnection()->config();
        $names = explode('\\', $config['driver']);
        $driver = strtolower($names[count($names) - 1]);
        if(isset($drivers[$driver])) {
            return $drivers[$driver];
        } else {
            return '';
        }
    }

    /**
     * ログメンテナンス用の view 変数を生成
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForLogMaintenance(): array
    {
        $fileSize = 0;
        if (file_exists(LOGS) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(LOGS,
                        \FilesystemIterator::CURRENT_AS_FILEINFO |
                        \FilesystemIterator::KEY_AS_PATHNAME |
                        \FilesystemIterator::SKIP_DOTS
                )
            );
            foreach($files as $file) {
                $fileSize += $file->getSize();
            }
        }
        return [
            'fileSize' => $fileSize,
            'zipEnable' => extension_loaded('zip'),
        ];
    }

}
