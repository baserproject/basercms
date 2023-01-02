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
use BaserCore\Utility\BcSiteConfig;
use Cake\Http\Exception\NotFoundException;

/**
 * Class DblogsController
 */
class DblogsController extends BcAdminAppController
{

    /**
     * [ADMIN] DBログ一覧
     *
     * @param DblogsServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(DblogsServiceInterface $service)
    {
        $this->setViewConditions('Dblog', ['default' => ['query' => [
            'limit' => BcSiteConfig::get('admin_list_num'),
            'sort' => 'id',
            'direction' => 'desc',
        ]]]);

        try {
            $entities = $this->paginate($service->getIndex($this->getRequest()->getQueryParams()));
        } catch (NotFoundException $e) {
            return $this->redirect(['action' => 'index']);
        }
        $this->set('dblogs', $entities);
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
