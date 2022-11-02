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

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Entity\Site;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * AppService
 */
class AppService
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * アプリケーション全体で必要な変数を取得
     * 
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForAll(): array
    {
        return [
            'currentSite' => $this->getCurrentSite(),
            'otherSites' => $this->getOtherSiteList(),
            'loginUser' => BcUtil::loginUser(),
            'currentAdminTheme' => BcUtil::getCurrentAdminTheme()
        ];
    }

    /**
     * 現在の管理対象のサイトを取得する
     * 
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCurrentSite(): ?Site
    {
        if(!BcUtil::loginUser()) return null;
        $site = Router::getRequest()->getAttribute('currentSite');
        if($site) {
            $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
            return $sitesTable->find()->where(['id' => $site->id])->first();
        } else {
            return null;
        }
    }

    /**
     * 現在の管理対象のサイト以外のリストを取得する
     * 
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getOtherSiteList(): array
    {
        if(!BcUtil::loginUser()) return [];
        $site = $this->getCurrentSite();
        if($site) {
            return $this->getService(SitesServiceInterface::class)->getList([
                'excludeIds' => $site->id,
                'status' => null
            ]);
        } else {
            return [];
        }
    }

}
