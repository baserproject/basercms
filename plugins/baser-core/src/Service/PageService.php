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

use Authentication\Identity;
use BaserCore\Model\Entity\Page;
use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Http\Cookie\Cookie;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Routing\Router;
use DateTime;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PageService
 * @package BaserCore\Service
 * @property PagesTable $Pages
 */
class PageService implements PageServiceInterface
{

    /**
     * PageService constructor.
     */
    public function __construct()
    {
        $this->Pages = TableRegistry::getTableLocator()->get('BaserCore.Pages');
    }

    /**
     * ユーザーの新規データ用の初期値を含んだエンティティを取得する
     * @return Page

     */
    public function getNew(): EntityInterface
    {
        return $this->Pages->newEntity([
            'user_groups' => [
                '_ids' => [1],
            ]], [
                'validate' => false,
            ]);
    }

    /**
     * ユーザーを取得する
     * @param int $id
     * @return Page

     */
    public function get($id): Page
    {
        return $this->Pages->get($id, [
        ]);
    }

    /**
     * ユーザー管理の一覧用のデータを取得
     * @param array $queryParams
     * @return Query

     */
    public function getIndex(array $queryParams): Query
    {
        $query = $this->Pages->find('all')->contain('UserGroups');

        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
        }

        if (!empty($queryParams['user_group_id'])) {
            $query->matching('UserGroups', function($q) use ($queryParams) {
                return $q->where(['UserGroups.id' => $queryParams['user_group_id']]);
            });
        }
        if (!empty($queryParams['name'])) {
            $query->where(['name LIKE' => '%' . $queryParams['name'] . '%']);
        }
        return $query;
    }

    /**
     * ユーザー登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException

     */
    public function create(array $postData)
    {
        $page = $this->Pages->newEmptyEntity();
        $page = $this->Pages->patchEntity($page, $postData, ['validate' => 'new']);
        return $this->Pages->saveOrFail($page);
    }

    /**
     * ユーザー情報を更新する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException

     */
    public function update(EntityInterface $target, array $postData)
    {
        $page = $this->Pages->patchEntity($target, $postData);
        return $this->Pages->saveOrFail($page);
    }

    /**
     * ユーザー情報を削除する
     * 最後のシステム管理者でなければ削除
     * @param int $id
     * @return bool

     */
    public function delete($id)
    {
        $page = $this->get($id);
        if ($page->isAdmin()) {
            $count = $this->Pages
                ->find('all', ['conditions' => ['PagesUserGroups.user_group_id' => Configure::read('BcApp.adminGroupId')]])
                ->join(['table' => 'users_user_groups',
                    'alias' => 'PagesUserGroups',
                    'type' => 'inner',
                    'conditions' => 'PagesUserGroups.user_id = Pages.id'])
                ->count();
            if ($count === 1) {
                throw new Exception(__d('baser', '最後のシステム管理者は削除できません'));
            }
        }
        return $this->Pages->delete($page);
    }
}
