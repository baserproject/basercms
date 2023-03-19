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

use BaserCore\Utility\BcUtil;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Routing\Router;

/**
 * Class DblogsService
 */
class DblogsService implements DblogsServiceInterface
{

    /**
     * Dblogs Table
     * @var \Cake\ORM\Table
     */
    private $Dblogs;

    /**
     * DblogsService constructor.
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->Dblogs = TableRegistry::getTableLocator()->get('BaserCore.Dblogs');
    }

    /**
     * リスト取得
     * 対応しない
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return [];
    }

    /**
     * 削除
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $dblog = $this->Dblogs->get($id);
        return $this->Dblogs->delete($dblog);
    }

    /**
     * 単一データ取得
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id): EntityInterface
    {
        return $this->Dblogs->get($id);
    }

    /**
     * 初期データ取得
     *
     * @param string $message
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew($message = ''): EntityInterface
    {
        return $this->Dblogs->newEntity([]);
    }

    /**
     * ログ更新
     * 対応しない
     *
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface
    {
        return null;
    }

    /**
     * DBログ登録
     *
     * @param array $data
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData = []): ?EntityInterface
    {
        $request = Router::getRequest();
        $data = [
            'message' => $postData['message'],
            'controller' => $request->getParam('controller'),
            'action' => $request->getParam('action')
        ];
        // TODO フロントでのログイン対応のためBcUtilではなくBcAuthComponentを使用する
        $user = BcUtil::loginUser();
        if ($user) {
            $data['user_id'] = $user->id;
        }
        $dblog = $this->Dblogs->newEntity($data);
        return $this->Dblogs->saveOrFail($dblog);
    }

    /**
     * DBログ一覧を取得
     *
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $queryParams = array_merge([
            'contain' => ['Users']
        ], $queryParams);

        $query = $this->Dblogs->find()->contain($queryParams['contain']);

        if (!empty($queryParams['message'])) {
            $query->where(['message LIKE' => '%' . $queryParams['message'] . '%']);
        }
        if (!empty($queryParams['user_id'])) {
            $query->where(['user_id' => $queryParams['user_id']]);
        }

        return $query;
    }

    /**
     * 最新のDBログ一覧を取得
     *
     * @param int $limit
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDblogs(int $limit): ResultSetInterface
    {
        return $this->Dblogs
            ->find('all')
            ->contain('Users')
            ->order(['Dblogs.id' => 'DESC'])
            ->limit($limit)
            ->all();
    }

    /**
     * DBログをすべて削除
     *
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteAll(): int
    {
        return $this->Dblogs->deleteAll(['1']);
    }

}
