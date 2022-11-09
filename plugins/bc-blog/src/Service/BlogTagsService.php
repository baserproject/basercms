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

namespace BcBlog\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BcBlog\Model\Entity\BlogTag;
use Cake\ORM\TableRegistry;

/**
 * BlogTagsService
 */
class BlogTagsService implements BlogTagsServiceInterface
{

    /**
     * Construct
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogTags = TableRegistry::getTableLocator()->get("BcBlog.BlogTags");
    }

    public function getNew()
    {
        return $this->BlogTags->newEmptyEntity();
    }

    /**
     * ブログタグ作成
     *
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     */
    public function create(array $postData)
    {
        $blogTags = $this->BlogTags->newEmptyEntity();
        $blogTags = $this->BlogTags->patchEntity($blogTags, $postData);
        return $this->BlogTags->saveOrFail($blogTags);
    }

    public function get($id)
    {
        return $this->BlogTags->get($id);
    }

    /**
     * ブログタグ一覧を取得
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     */
    public function getIndex(array $queryParams)
    {
        $query = $this->BlogTags->find();
        if(!empty($queryParams['name'])) {
            $query->where(['name LIKE' => '%' . urldecode($queryParams['name']) . '%']);
        }
        return $query;
    }

    public function update(BlogTag $blogTag, $postData)
    {
        $blogTag = $this->BlogTags->patchEntity($blogTag, $postData);
        return $this->BlogTags->saveOrFail($blogTag);
    }

    public function delete(int $id) {
        $blogTag = $this->get($id);
        return $this->BlogTags->delete($blogTag);
    }

    /**
     * IDからタイトルリストを取得する
     *
     * @param array $ids
     * @return array
     * @checked
     * @noTodo
     */
    public function getTitlesById(array $ids): array
    {
        return $this->BlogTags->find('list')->select(['id', 'name'])->where(['id IN' => $ids])->toArray();
    }

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->BlogTags->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

}
