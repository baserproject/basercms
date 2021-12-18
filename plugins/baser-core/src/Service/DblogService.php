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

use BaserCore\Utility\BcUtil;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Error\BcException;
use Cake\Routing\Router;

/**
 * Class DblogService
 */
class DblogService implements DblogServiceInterface
{

    /**
     * Dblogs Table
     * @var \Cake\ORM\Table
     */
    private $Dblogs;

    /**
     * DblogService constructor.
     */
    public function __construct()
    {
        $this->Dblogs = TableRegistry::getTableLocator()->get('BaserCore.Dblogs');
    }

    /**
     * DBログ登録
     * @param array $data
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(string $message): EntityInterface
    {
        $request = Router::getRequest();
        $data = [
            'message' => $message,
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
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams): Query
    {
        $options = [];
        $query = $this->Dblogs
            ->find('all', $options)
            ->contain('Users');

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
     * @param int $limit
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDblogs(int $limit): object
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
