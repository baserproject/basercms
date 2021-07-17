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

namespace BaserCore\Service\Admin;

use BaserCore\Service\SiteConfigsTrait;
use BaserCore\Service\SitesService;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Core\Configure;

/**
 * Class UserGroupManageService
 * @package BaserCore\Service
 */
class SiteManageService extends SitesService implements SiteManageServiceInterface
{

    /**
     * Trait
     */
    use SiteConfigsTrait;

    /**
     * 言語リストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLangs(): array
    {
        $languages = Configure::read('BcLang');
        $langs = [];
        foreach($languages as $key => $lang) {
            $langs[$key] = $lang['name'];
        }
        return $langs;
    }

    /**
     * デバイスリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDevices(): array
    {
        $agents = Configure::read('BcAgent');
        $devices = [];
        foreach($agents as $key => $agent) {
            $devices[$key] = $agent['name'];
        }
        return $devices;
    }

    /**
     * サイトのリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteList(): array
    {
        return $this->Sites->getSiteList();
    }

}
