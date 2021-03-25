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

namespace BaserCore\View\Helper;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcAdminHelper
 * @package BaserCore\View\Helper
 * @uses BcAdminHelper
 * @property BcBaserHelper $BcBaser
 */
class BcAdminHelper extends Helper
{

    /**
     * Helper
     * @var string[]
     */
    public $helpers = ['BcBaser'];

	/**
	 * 管理システムグローバルメニューの利用可否確認
	 *
	 * @return bool
	 */
	public function isAdminGlobalmenuUsed()
	{
	    return true;
	    // TODO 要コード確認
	    /* >>>
		if (!BC_INSTALLED) {
			return false;
		}
		if (Configure::read('BcRequest.isUpdater')) {
			return false;
		}
		$user = $this->_View->get('user');
		if (!$user) {
			return false;
		}
		$UserGroup = ClassRegistry::init('UserGroup');
		return $UserGroup->isAdminGlobalmenuUsed($user['user_group_id']);
	    <<< */
	}

	/**
	 * ログインユーザーがシステム管理者かチェックする
	 *
	 * @return boolean
	 */
	public function isSystemAdmin()
	{
		$user = $this->_View->getVar('user');
		if (empty($this->request->params['admin']) || !$user) {
			return false;
		}
		if ($user['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
			return true;
		}
		return false;
	}

    /**
     * JSON形式でメニューデータを取得する
     * # siteId の仕様
     * - null：全てのサイトで表示
     * - 数値：対象のサイトのみ表示（javascript で扱いやすいよう文字列に変換）
     * @return string
     */
    public function getJsonMenu()
    {
        $adminMenuGroups = Configure::read('BcApp.adminNavigation');

        $currentSiteId = (string)$this->_View->getRequest()
            ->getSession()
            ->read('Baser.viewConditions.ContentsAdminIndex.named.site_id');

        if (!$adminMenuGroups) {
            return null;
        }
        // TODO : 要実装
//		if(empty($this->_View->viewVars['user']['user_group_id'])) {
//			return null;
//		}
        if (!is_null($currentSiteId) && $currentSiteId) {
            $currentSiteId = (string)$currentSiteId;
        } else {
            $currentSiteId = "0";
        }
        $request = $this->_View->getRequest();
        $base = $request->getAttributes()['base'];
        $url = $request->getUri();
        $currentUrl = '/' . $url;
        $params = null;
        if (strpos($currentUrl, '?') !== false) {
            [$currentUrl, $params] = explode('?', $currentUrl);
        }
//		$currentUrl = preg_replace('/\/index$/', '/', $currentUrl);
        if ($params) {
            $currentUrl .= '?' . $params;
        }
        $contents = $adminMenuGroups['Contents'];
        $systems = $adminMenuGroups['Systems'];
        $plugins = (isset($adminMenuGroups['Plugins']))? $adminMenuGroups['Plugins'] : [];
        unset($adminMenuGroups['Contents'], $adminMenuGroups['Systems'], $adminMenuGroups['Plugins']);
        if ($plugins) {
            foreach($plugins['menus'] as $plugin) {
                $systems['Plugin']['menus'][] = $plugin;
            }
        }
        $adminMenuGroups = $contents + $adminMenuGroups + $systems;
        // TODO 要実装
//		$Permission = ClassRegistry::init('Permission');
        $covertedAdminMenuGroups = [];
        $currentOn = false;
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
                $adminMenuGroup['siteId'] = (string)$adminMenuGroup['siteId'];
            }
            if (!isset($adminMenuGroup['type'])) {
                $adminMenuGroup = array_merge(['type' => null], $adminMenuGroup);
            }
            $adminMenuGroup = array_merge(['name' => $group], $adminMenuGroup);

            if (!empty($adminMenuGroup['url'])) {

                $adminMenuGroup['url'] = preg_replace('/^' . preg_quote($base, '/') . '\//', '/', $this->BcBaser->getUrl($adminMenuGroup['url']));
                if (preg_match('/^' . preg_quote($adminMenuGroup['url'], '/') . '$/', $currentUrl)) {
                    $adminMenuGroup['current'] = true;
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
                    // TODO : 要実装
//					if ($Permission->check($url, $this->_View->viewVars['user']['user_group_id'])) {
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
//					}
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
        $loginAction = Configure::read('BcPrefixAuth.' . $prefix . '.loginAction');
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
        $this->_View->set('title', $title);
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
        echo h($this->_View->fetch('title'));
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
            echo $this->_View->element('Admin/help', ['help' => $template]);
        }
    }

    /**
     * Search
     * @checked
     * @noTodo
     */
    public function search(): void
    {
        $template = $this->_View->get('search');
        $contentsName = $this->BcBaser->getContentsName(true);
        $adminSearchOpened = $this->_View->getRequest()->getSession()->read('BcApp.adminSearchOpened.' . $contentsName);
        $adminSearchOpenedSaveUrl = $this->BcBaser->getUrl([
            'controller' => 'Utilities',
            'action' => 'ajax_save_search_box',
            $contentsName
        ]);
        if ($template) {
            echo $this->_View->element('Admin/search', [
                'search' => $template,
                'adminSearchOpened' => $adminSearchOpened,
                'adminSearchOpenedSaveUrl' => $adminSearchOpenedSaveUrl
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
        echo $this->_View->element('Admin/contents_menu', [
            'isHelp' => (bool)($this->_View->get('help')),
            'isLogin' => (bool)(BcUtil::loginUser())
        ]);
    }

}
