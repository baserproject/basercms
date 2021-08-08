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
     * @param  int $siteId
     * @return Query
     * @checked
     * @unitTest
     */
    public function getTreeIndex($siteId): Query
    {
        if ($siteId === 'all') {
            $conditions = ['or' => [
                ['Sites.use_subdomain' => false],
                ['Contents.site_id' => 0]
            ]];
        } else {
            $conditions = ['Contents.site_id' => $siteId];
        }
        // TODO: contain(['Sites'])動かない
        return $this->Contents->find('threaded')->where([$conditions])->order(['lft'])->contain(['Sites']);
    }

    /**
     * getTableIndex
     *
     * @param  int $siteId
     * @param  array $searchData
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTableIndex($siteId, $conditions): Query
    {
        $conditions = array_merge(['site_id' => $siteId], $conditions);

        return $this->Contents->find('all')->where($conditions);
    }

    /**
     * getTrashIndex
     *
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTrashIndex(): Query
    {
        // $this->Contents->Behaviors->unload('SoftDelete');
        return $this->Contents->find('threaded')->where(['deleted' => true])->order(['site_id', 'lft']);
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

