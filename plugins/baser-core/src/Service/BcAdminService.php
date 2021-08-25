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

use BaserCore\Model\Entity\Site;
use BaserCore\Utility\BcContainerTrait;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcAdminService
 */
class BcAdminService implements BcAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
    }

    /**
     * 現在の管理対象のサイトを設定する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setCurrentSite(): void
    {
        $request = Router::getRequest();
        $session = $request->getSession();
        $currentSiteId = 1;
        $queryCurrentSiteId = $request->getQuery('current_site_id');
        if (!$session->check('BcApp.Admin.currentSite') || $queryCurrentSiteId) {
            if ($queryCurrentSiteId) {
                $currentSiteId = $queryCurrentSiteId;
            }
            $session->write('BcApp.Admin.currentSite', $this->Sites->get($currentSiteId));
        }
    }

    /**
     * 現在の管理対象のサイトを取得する
     * @return Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentSite(): Site
    {
        $site = Router::getRequest()->getSession()->read('BcApp.Admin.currentSite');
        if(!$site) {
            $site = $this->Sites->getRootMain();
        }
        return $site;
    }

    /**
     * 現在の管理対象のサイト以外のリストを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getOtherSiteList(): array
    {
        return $this->Sites->getList(null, ['excludeIds' => [$this->getCurrentSite()->id]]);
    }

}
