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
use BcBlog\Model\Table\BlogCommentsTable;
use Cake\ORM\TableRegistry;

/**
 * BlogCommentsService
 *
 * @property BlogCommentsTable $BlogComments
 */
class BlogCommentsService implements BlogCommentsServiceInterface
{

    /**
     * ブログコメントを初期化する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogComments = TableRegistry::getTableLocator()->get('BcBlog.BlogComments');
    }

    /**
     * ブログコメント一覧データを取得する
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams)
    {
        $options = array_merge([
            'blog_post_id' => null
        ], $queryParams);
        $query = $this->BlogComments->find()->contain(['BlogPosts']);
        if(!empty($queryParams['num'])) {
            $query = $query->limit($queryParams['num']);
        }
        if(!empty($options['blog_post_id'])) {
            $query = $query->where(['BlogComments.blog_post_id' => $options['blog_post_id']]);
        }
        return $query;
    }

    /**
     * ブログコメントの単一データを取得する
     *
     * @param int $id
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id) {
        return $this->BlogComments->get($id, ['contain' => ['BlogPosts']]);
    }

    /**
     * ブログコメントを公開状態に設定する
     *
     * @param int $id
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish(int $id)
    {
        $entity = $this->get($id);
        $entity->status = true;
        return $this->BlogComments->save($entity);
    }

    /**
     * ブログコメントを非公開状態に設定する
     *
     * @param int $id
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish(int $id)
    {
        $entity = $this->get($id);
        $entity->status = false;
        return $this->BlogComments->save($entity);
    }

    /**
     * ブログコメントを削除する
     *
     * @param int $id
     * @return bool
     * @noTodo
     * @checked
     * @unitTest
     */
    public function delete(int $id)
    {
        $entity = $this->get($id);
        return $this->BlogComments->delete($entity);
    }

    /**
     * アップロード対象となるフィールドに格納するファイル名を、指定したフィールドの値を利用したファイル名に変更する
     *
     * ### リネーム例
     *  - 元ファイル名が、sample.png
     *  - id フィールドを利用する
     *  - id に 585 が入っている
     *  - nameformat が %08d となっている
     *
     * 結果：00000585.png
     *
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->BlogComments->getConnection();
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
