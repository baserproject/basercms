<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Model\Table\PluginsTable;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Utility\Xml;
use Exception;

/**
 * Class PluginManageService
 * @package BaserCore\Service
 * @property PluginsTable $Plugins
 */

class PluginManageService extends PluginsService implements PluginManageServiceInterface
{
    /**
     * ユーザー一覧を取得
     * @param string $sortMode
     * @return array $plugins
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getIndex(string $sortMode): array
    {
        return parent::getIndex($sortMode);
    }

    /**
     * プラグインを無効にする
     * @param string $name
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(string $name): bool
    {
        return parent::detach(urldecode($name));
    }

    /**
     * データベースをリセットする
     *
     * @param string $name
     * @param array $options
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetDb(string $name, $options = []):void
    {
        if(isset($options['connection'])) {
            $options = ['connection' => $options['connection']];
        } else {
            $options = [];
        }
        parent::resetDb($name, $options);
    }

    /**
     * プラグインを削除する
     * @param string $name
     * @param array $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(string $name, array $options = []): void
    {
        if(isset($options['connection'])) {
            $options = ['connection' => $options['connection']];
        } else {
            $options = [];
        }
        parent::uninstall(urldecode($name), $options);
    }

    /**
     * baserマーケットのプラグイン一覧を取得する
     * @return array|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMarketPlugins(): array
    {
        return parent::getMarketPlugins();
    }

}
