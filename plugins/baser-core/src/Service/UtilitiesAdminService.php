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

namespace BaserCore\Service;

use BaserCore\Utility\BcSiteConfig;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * UtilitiesAdminService
 */
class UtilitiesAdminService implements UtilitiesAdminServiceInterface
{

    /**
     * info 画面用の view 変数を生成
     * @return array
     */
    public function getViewVarsForInfo(): array
    {
        return [
            'datasource' => $this->_getDriver(),
            'baserVersion' => BcSiteConfig::get('version'),
            'cakeVersion' => Configure::version()
        ];
    }

    /**
     * データベースのドライバー情報を取得
     * @return string
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

}
