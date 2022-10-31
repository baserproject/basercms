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

namespace BaserCore\Controller\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;

/**
 * Class DblogsController
 */
class DblogsController extends BcAdminAppController
{

    /**
     * [ADMIN] DBログ一覧
     *
     * @param DblogsServiceInterface $service
     * @param SiteConfigsServiceInterface $siteConfigService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(DblogsServiceInterface $service, SiteConfigsServiceInterface $siteConfigService)
    {
        $this->setViewConditions('Dblog', ['default' => ['query' => [
            'limit' => $siteConfigService->getValue('admin_list_num'),
            'sort' => 'id',
            'direction' => 'desc',
        ]]]);

        $queryParams = $this->request->getQueryParams();

        $this->set('dblogs', $this->paginate($service->getIndex($queryParams)));
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * [ADMIN] 最近の動きを削除
     *
     * @param DblogsServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete_all(DblogsServiceInterface $service)
    {
        if (!$this->request->is('post')) {
            $this->notFound();
        }

        if ($service->deleteAll()) {
            $this->BcMessage->setInfo(__d('baser', '最近の動きのログを削除しました。'));
        } else {
            $this->BcMessage->setError(__d('baser', '最近の動きのログ削除に失敗しました。'));
        }
        $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
    }
}
