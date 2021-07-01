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
use BaserCore\Model\Table\SitesTable;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SitesService
 * @package BaserCore\Service
 * @property SitesTable $Sites
 */
class SitesService implements SitesServiceInterface
{

    use SiteConfigsTrait;

    /**
     * Sites Table
     * @var \Cake\ORM\Table
     */
    public $Sites;

    /**
     * SitesService constructor.
     */
    public function __construct()
    {
        $this->Sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
    }

    /**
     * サイトの新規データ用の初期値を含んだエンティティを取得する
     * @return Site
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): Site
    {
        return $this->Sites->newEntity([
            'title' => $this->getSiteConfig('name'),
            'status' => false,
        ]);
    }

    /**
     * サイトを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Sites->get($id);
    }

    /**
     * サイト管理の一覧用のデータを取得
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams): Query
    {
        $options = [];
        if (!empty($queryParams['num'])) {
            $options = ['limit' => $queryParams['num']];
        }
        $query = $this->Sites->find('all', $options);
        if (!empty($queryParams['name'])) {
            $query->where(['name LIKE' => '%' . $queryParams['name'] . '%']);
        }
        if (isset($queryParams['status'])) {
            $query->where(['status' => $queryParams['status']]);
        }
        return $query;
    }

    /**
     * サイト登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $site = $this->Sites->newEmptyEntity();
        $site = $this->Sites->patchEntity($site, $postData);
        return ($result = $this->Sites->save($site))? $result : $site;
    }

    /**
     * サイト情報を更新する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData)
    {
        $site = $this->Sites->patchEntity($target, $postData);
        return ($result = $this->Sites->save($target))? $result : $site;
    }

    /**
     * サイト情報を削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id)
    {
        $site = $this->get($id);
        return $this->Sites->delete($site);
    }

}
