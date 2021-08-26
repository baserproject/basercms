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

namespace BaserCore\Controller\Admin;

use BaserCore\Service\DblogServiceInterface;
use BaserCore\Service\SiteConfigTrait;
use BaserCore\Service\UserServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class DblogsController
 * @package BaserCore\Controller\Admin
 */
class DblogsController extends BcAdminAppController
{
    /**
     * SiteConfigTrait
     */
    use SiteConfigTrait;

    /**
     * [ADMIN] DBログ一覧
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(DblogServiceInterface $DblogService, UserServiceInterface $userService)
    {
        $this->setViewConditions('Dblog', ['default' => ['query' => [
            'num' => $userService->getSiteConfig('admin_list_num'),
            'sort' => 'id',
            'direction' => 'desc',
        ]]]);

        $queryParams = $this->request->getQueryParams();
        if (!empty($queryParams['num'])) {
            $this->paginate['limit'] = $queryParams['num'];
        }

        $this->set('dblogs', $this->paginate($DblogService->getIndex($queryParams)));
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * [ADMIN] 最近の動きを削除
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete_all(DblogServiceInterface $DblogService)
    {
        if (!$this->request->is('post')) {
            $this->notFound();
        }

        if ($DblogService->deleteAll()) {
            $this->BcMessage->setInfo(__d('baser', '最近の動きのログを削除しました。'));
        } else {
            $this->BcMessage->setError(__d('baser', '最近の動きのログ削除に失敗しました。'));
        }
        $this->redirect(['controller' => 'dashboard', 'action' => 'index']);
    }
}
