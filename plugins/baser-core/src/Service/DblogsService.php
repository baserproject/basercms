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

use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Error\BcException;

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
     */
    public function __construct()
    {
        $this->Dblogs = TableRegistry::getTableLocator()->get('BaserCore.Dblogs');
    }

    /**
     * DBログ登録
     * @param array $data
     * @return object
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $data): object
    {
        $dblog = $this->Dblogs->newEntity($data);
        $savedDblog = $this->Dblogs->save($dblog);
        if (!$savedDblog) {
            throw new BcException(__d('baser', 'DBログの保存に失敗しました。'));
        }
        return $savedDblog;
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
