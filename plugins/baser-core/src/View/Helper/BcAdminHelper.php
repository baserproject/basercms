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

namespace BaserCore\View\Helper;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class BcAdminHelper
 * @uses BcAdminHelper
 * @property BcBaserHelper $BcBaser
 * @property BcContentsHelper $BcContents
 */
class BcAdminHelper extends Helper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * Helper
     * @var string[]
     */
    public $helpers = ['BaserCore.BcBaser', 'BaserCore.BcAuth', 'BaserCore.BcContents'];

    /**
     * ログインユーザーがシステム管理者かチェックする
     *
     * @return boolean
     */
    public function isSystemAdmin()
    {
        $user = $this->_View->getVar('user');
        if ($this->request->getParam['prefix'] === 'Admin' || !$user) {
            return false;
        }
        if ($user['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
            return true;
        }
        return false;
    }

    /**
     * 管理画面のメニューを取得する
     * @return array|false
     * @checked
     * @unitTest
     * @noTodo
     */
    private function getAdminMenuGroups()
    {
        $adminMenuGroups = Configure::read('BcApp.adminNavigation');

        if($adminMenuGroups) {
            $contents = $adminMenuGroups['Contents'];
            $systems = $adminMenuGroups['Systems'];
            $plugins = $adminMenuGroups['Plugins'] ?? [];

            unset($adminMenuGroups['Contents'], $adminMenuGroups['Systems'], $adminMenuGroups['Plugins']);
            if ($plugins) {
                foreach($plugins['menus'] as $plugin) {
                    $systems['Plugin']['menus'][] = $plugin;
                }
            }
            $adminMenuGroups = $contents + $adminMenuGroups + $systems;
            return $adminMenuGroups;
        } else {
            return false;
        }
    }

    /**
     * 管理画面のメニューに変更を加える
     * @todo 整理する必要あり
     * @return array
     */
    private function convertAdminMenuGroups($adminMenuGroups)
    {
        $request = $this->_View->getRequest();
        $base = $request->getAttributes()['base'];
        $currentUrl = $request->getPath();
        $params = null;

        if (strpos($currentUrl, '?') !== false) {
            [$currentUrl, $params] = explode('?', $currentUrl);
        }
//		$currentUrl = preg_replace('/\/index$/', '/', $currentUrl);
        if ($params) {
            $currentUrl .= '?' . $params;
        }
        $covertedAdminMenuGroups = [];
        $currentOn = false;
        /* @var PermissionsService $permissionsService */
        $permissionsService = $this->getService(PermissionsServiceInterface::class);
        foreach($adminMenuGroups as $group => $adminMenuGroup) {
            if (!empty($adminMenuGroup['disable']) && $adminMenuGroup['disable'] === true) {
                continue;
            }
            if (!isset($adminMenuGroup['icon'])) {
                $adminMenuGroup['icon'] = 'bca-icon--file';
            }
            $adminMenuGroup = array_merge(['current' => false], $adminMenuGroup);
            if (!isset($adminMenuGroup['siteId'])) {
                $adminMenuGroup = array_merge(['siteId' => null], $adminMenuGroup);
            } else {
                $adminMenuGroup['siteId'] = (int) $adminMenuGroup['siteId'];
            }
            if (!isset($adminMenuGroup['type'])) {
                $adminMenuGroup = array_merge(['type' => null], $adminMenuGroup);
            }
            $adminMenuGroup = array_merge(['name' => $group], $adminMenuGroup);

            if (!empty($adminMenuGroup['url'])) {
                $adminMenuGroup['url'] = preg_replace('/^' . preg_quote($base, '/') . '\//', '/', $this->BcBaser->getUrl($adminMenuGroup['url']));
                if ($permissionsService->check($adminMenuGroup['url'], Hash::extract(BcUtil::loginUserGroup(), '{n}.id'))) {
                    if (preg_match('/^' . preg_quote($adminMenuGroup['url'], '/') . '$/', $currentUrl)) {
                        $adminMenuGroup['current'] = true;
                    }
                } else {
                    unset($adminMenuGroup['url']);
                }
            }

            $covertedAdminMenus = [];
            if (!empty($adminMenuGroup['menus'])) {
                foreach($adminMenuGroup['menus'] as $menu => $adminMenu) {
                    if (!empty($adminMenu['disable']) && $adminMenu['disable'] === true) {
                        continue;
                    }
                    if (!isset($adminMenu['icon'])) {
                        $adminMenu['icon'] = '';
                    }
                    $adminMenu['name'] = $menu;
                    $url = $this->BcBaser->getUrl($adminMenu['url']);
                    $url = preg_replace('/^' . preg_quote($base, '/') . '\//', '/', $url);
					if (!$permissionsService->check($url, Hash::extract(BcUtil::loginUserGroup(), '{n}.id'))) continue;
                    if (empty($adminMenuGroup['url'])) {
                        $adminMenuGroup['url'] = $url;
                    }
                    $adminMenu['urlArray'] = $adminMenu['url'];
                    $adminMenu['url'] = $url;
                    if (preg_match('/^' . preg_quote($url, '/') . '$/', $currentUrl)) {
                        $adminMenu['current'] = true;
                        $adminMenuGroup['current'] = false;
                        $adminMenuGroup['expanded'] = true;
                        $currentOn = true;
                    }
                    $covertedAdminMenus[] = $adminMenu;
                }
            }
            if ($covertedAdminMenus) {
                $adminMenuGroup['menus'] = $covertedAdminMenus;
            } else {
                $adminMenuGroup['menus'] = [];
            }
            if (!empty($adminMenuGroup['url']) || $adminMenuGroup['menus']) {
                $covertedAdminMenuGroups[] = $adminMenuGroup;
            }
        }
        if ($currentOn === false) {
            foreach($covertedAdminMenuGroups as $key => $adminMenuGroup) {
                if (!empty($adminMenuGroup['disable']) && $adminMenuGroup['disable'] === true) {
                    continue;
                }
                foreach($adminMenuGroup['menus'] as $menu => $adminMenu) {
                    if ((!empty($adminMenu['disable']) && $adminMenu['disable'] === true) || empty($adminMenu['currentRegex'])) {
                        continue;
                    }
                    if (preg_match($adminMenu['currentRegex'], $currentUrl)) {
                        $covertedAdminMenuGroups[$key]['menus'][$menu]['current'] = true;
                        $covertedAdminMenuGroups[$key]['current'] = false;
                        $covertedAdminMenuGroups[$key]['expanded'] = true;
                        $currentOn = true;
                        break;
                    }
                }
                if ($currentOn === true) {
                    break;
                }
            }
        }

        return $covertedAdminMenuGroups;
    }

    /**
     * JSON形式でメニューデータを取得する
     * # siteId の仕様
     * - null：全てのサイトで表示
     * - 数値：対象のサイトのみ表示（javascript で扱いやすいよう文字列に変換）
     * @return string
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getJsonMenu()
    {
        $adminMenuGroups = $this->getAdminMenuGroups();
        if($adminMenuGroups === false) return null;
        $loginUserGroup = BcUtil::loginUserGroup();
        if($loginUserGroup === false) return null;
        $currentSiteId = 1;
        if($currentSite = $this->_View->getRequest()->getAttribute('currentSite')) {
            $currentSiteId = $currentSite->id;
        }

        $covertedAdminMenuGroups = $this->convertAdminMenuGroups($adminMenuGroups);

        $menuSettings = [
            'currentSiteId' => $currentSiteId,
            'menuList' => $covertedAdminMenuGroups
        ];
        return json_encode($menuSettings);
    }

    /**
     * 管理画面の画面タイトルの横に配置するボタンをを追加する
     *
     * @param array $links ['url' => string or array, 'confirm' => 'confirm message', 'something attributes' => 'attr value']
     * @checked
     * @unitTest
     * @noTodo
     */
    public function addAdminMainBodyHeaderLinks($links)
    {
        $mainBodyHeaderLinks = $this->_View->get('mainBodyHeaderLinks');
        if ($mainBodyHeaderLinks === null) {
            $mainBodyHeaderLinks = [];
        }
        $mainBodyHeaderLinks[] = $links;
        $this->_View->set('mainBodyHeaderLinks', $mainBodyHeaderLinks);
    }

    /**
     * サイドバーが利用可能か確認する
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAvailableSideBar()
    {
        $prefix = $this->_View->getRequest()->getParam('prefix');
        $loginAction = Router::url(Configure::read('BcPrefixAuth.' . $prefix . '.loginAction'));
        $name = $this->_View->getName();
        $url = $this->_View->getRequest()->getPath();
        if (!in_array($name, ['Installations', 'Updaters']) && ($loginAction !== $url && !empty(BcUtil::loginUser()))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set Title
     * @param string $title
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setTitle($title): void
    {
        $this->_View->assign('title', $title);
    }

    /**
     * Set Help
     * @param string $template
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setHelp($template): void
    {
        $this->_View->set('help', $template);
    }

    /**
     * Set Search
     * @param string $template
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setSearch($template): void
    {
        $this->_View->set('search', $template);
    }

    /**
     * Title
     * @checked
     * @noTodo
     * @unitTest
     */
    public function title(): void
    {
        echo $this->getTitle();
    }

    /**
     * Get Title
     * @return string
     * @checked
     * @noTodo
     */
    public function getTitle(): string
    {
        return h($this->_View->fetch('title'));
    }

    /**
     * Help
     * @checked
     * @noTodo
     * @unitTest
     */
    public function help(): void
    {
        $template = $this->_View->get('help');
        if ($template) {
            echo $this->_View->element('help', ['help' => $template]);
        }
    }

    /**
     * Search
     * @checked
     * @unitTest
     * @noTodo
     */
    public function search(): void
    {
        $template = $this->_View->get('search');
        $contentsName = $this->BcBaser->getContentsName(true);
        $adminSearchOpened = $this->_View->getRequest()->getSession()->read('BcApp.adminSearchOpened.' . $contentsName);
        if ($template) {
            echo $this->_View->element('search', [
                'search' => $template,
                'adminSearchOpened' => $adminSearchOpened,
                'adminSearchOpenedTarget' => $contentsName
            ]);
        }
    }

    /**
     * Contents Menu
     * @checked
     * @noTodo
     * @unitTest
     */
    public function contentsMenu(): void
    {
        echo $this->_View->element('contents_menu', [
            'isHelp' => (bool)($this->_View->get('help')),
            'isLogin' => (bool)(BcUtil::loginUser()),
            'isSuperUser' => BcUtil::isSuperUser()
        ]);
    }


    /**
     * 編集画面へのリンクが存在するかチェックする
     *
     * @return bool 存在する場合は true を返す
     * @checked
     * @noTodo
     * @unitTest
     */
    public function existsEditLink()
    {
        return ($this->BcAuth->isCurrentUserAdminAvailable() && !empty($this->_View->get('editLink')));
    }

    /**
     * 公開ページへのリンクが存在するかチェックする
     *
     * @return bool リンクが存在する場合は true を返す
     * @checked
     * @noTodo
     * @unitTest
     */
    public function existsPublishLink()
    {
        return ($this->BcAuth->isCurrentUserAdminAvailable() && !empty($this->_View->get('publishLink')));
    }

    /**
     * 編集リンクを設定する
     * @param string|array $link
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setEditLink($link)
    {
        $this->_View->set('editLink', $link);
    }

    /**
     * 公開リンクを設定する
     * @param string $link
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setPublishLink($link)
    {
        $this->_View->set('publishLink', $link);
    }

    /**
     * 編集画面へのリンクを出力する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function editLink(): void
    {
        if ($this->existsEditLink()) {
            $this->BcBaser->link(__d('baser', '編集する'), $this->_View->get('editLink'), ['class' => 'tool-menu']);
        }
    }

    /**
     * 公開ページへのリンクを出力する
     *
     * 管理システムで利用する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publishLink(): void
    {
        if ($this->existsPublishLink()) {
            $siteManage = $this->getService(SitesServiceInterface::class);
            $site = $siteManage->findByUrl($this->_View->get('publishLink'));
            $useSubdomain = $fullUrl = false;
            if ($site && $site->name) {
                $useSubdomain = $site->use_subdomain;
                $fullUrl = true;
            }
            $url = $this->BcContents->getUrl($this->_View->get('publishLink'), $fullUrl, $useSubdomain, false);
            $this->BcBaser->link(__d('baser', 'サイト確認'), $url, ['class' => 'tool-menu']);
        }
    }

}
