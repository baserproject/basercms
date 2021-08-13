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

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

class ContentsService implements ContentsServiceInterface
{

    /**
     * Contents
     *
     * @var ContentsTable
     */
    public $Contents;

    /**
     * Sites
     *
     * @var SitesTable
     */
    public $Sites;

    public function __construct()
    {
        $this->Contents = TableRegistry::getTableLocator()->get("BaserCore.Contents");
        $this->Sites = TableRegistry::getTableLocator()->get("BaserCore.Sites");
    }

    /**
     * コンテンツを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface
    {
        return $this->Contents->get($id, [
            'contain' => ['Sites'],
        ]);
    }

    /**
     * getTreeIndex
     *
     * @param  array $queryParams
     * @return Query
     * @checked
     * @unitTest
     */
    public function getTreeIndex(array $queryParams): Query
    {
        if ($queryParams['site_id'] === 'all') {
            $queryParams = ['or' => [
                ['Sites.use_subdomain' => false],
                ['Contents.site_id' => 0]
            ]];
        }

        // TODO: contain(['Sites'])動かない
        // return $this->getIndex($queryParams, 'threaded')->order(['lft'])->contain(['Sites']);
        return $this->getIndex($queryParams, 'threaded')->order(['lft']);
    }

    /**
     * テーブルインデックス用の条件を返す
     *
     * @param  array $queryParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableConditions(array $queryParams): array
    {

        $conditions['site_id'] = $queryParams['site_id'];

        if ($queryParams['name']) {
            $conditions['OR'] = [
                'name LIKE' => '%' . $queryParams['name'] . '%',
                'title LIKE' => '%' . $queryParams['name'] . '%'
            ];
        }
        if ($queryParams['folder_id']) {
            $Contents = $this->Contents->find('all')->select(['lft', 'rght'])->where(['id' => $queryParams['folder_id']]);
            $conditions['rght <'] = $Contents->first()->rght;
            $conditions['lft >'] = $Contents->first()->lft;
        }
        if ($queryParams['author_id']) {
            $conditions['author_id'] = $queryParams['author_id'];
        }
        if ($queryParams['self_status'] !== '') {
            $conditions['self_status'] = $queryParams['self_status'];
        }
        if ($queryParams['type']) {
            $conditions['type'] = $queryParams['type'];
        }

        return $conditions;
    }

    /**
     * コンテンツ管理の一覧用のデータを取得
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams, ?string $type="all"): Query
    {
        $options = [];

        if (!empty($queryParams['num'])) {
            $options = ['limit' => $queryParams['num']];
            unset($queryParams['num']);
        }

        if (!empty($queryParams['name'])) {
            $queryParams['OR'] = [
                'name LIKE' => '%' . $queryParams['name'] . '%',
                'title LIKE' => '%' . $queryParams['name'] . '%'
            ];
        }

        if (isset($queryParams['status'])) {
            $queryParams['status'] =  $queryParams['status'];
        }
        return $this->Contents->find($type, $options)->where([$queryParams]);
    }

    /**
     * テーブル用のコンテンツ管理の一覧データを取得
     *
     * @param  array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableIndex(array $queryParams): Query
    {

        $conditions = [
            'open' => '1',
            'name' => '',
            'folder_id' => '',
            'type' => '',
            'self_status' => '1',
            'author_id' => '',
        ];
        if ($queryParams) {
            $queryParams = array_merge($conditions, $queryParams);
        }
        return $this->getIndex($this->getTableConditions($queryParams));
    }


    /**
     * getTrashIndex
     * @param  array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrashIndex(array $queryParams): Query
    {
        $queryParams = array_merge($queryParams, ['deleted' => true]);
        return $this->getIndex($queryParams, 'threaded')->order(['site_id', 'lft']);
    }

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     *
     * @param int $siteId
     * @param array $options
     * @return array|bool
     */
    public function getContentFolderList($siteId = null, $options = [])
    {
        $options = array_merge([
            'excludeId' => null
        ], $options);

        $conditions = [
            'type' => 'ContentFolder',
            'alias_id IS NULL'
        ];

        if (!is_null($siteId)) {
            $conditions['site_id'] = $siteId;
        }
        if ($options['excludeId']) {
            $conditions['id <>'] = $options['excludeId'];
        }
        if (!empty($options['conditions'])) {
            $conditions = array_merge($conditions, $options['conditions']);
        }
        $folders = $this->Contents->find('treeList')->where([$conditions]);
        if ($folders) {
            return $this->convertTreeList($folders->all()->toArray());
        }
        return false;
    }

    /**
     * ツリー構造のデータを コンボボックスのデータ用に変換する
     * @param $nodes
     * @return array
     */
    public function convertTreeList($nodes)
    {
        if (!$nodes) {
            return [];
        }
        foreach($nodes as $key => $value) {
            if (preg_match("/^([_]+)/i", $value, $matches)) {
                $value = preg_replace("/^[_]+/i", '', $value);
                $prefix = str_replace('_', '　　　', $matches[1]);
                $value = $prefix . '└' . $value;
            }
            $nodes[$key] = $value;
        }
        return $nodes;
    }
}

