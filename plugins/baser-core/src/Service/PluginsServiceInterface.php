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

/**
 * Interface PluginsServiceInterface
 * @package BaserCore\Service
 */
interface PluginsServiceInterface
{
    /**
     * プラグイン一覧を取得
     * @param string $sortMode
     * @return array $plugins
     */
    public function getIndex(string $sortMode): array;
        /**
     * プラグイン一覧を取得
     * @param string $name プラグイン名
     * @return EntityInterface
     */
    public function install($name, $data = []);

    public function getPluginConfig($name);
}
