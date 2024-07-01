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
use BcBlog\Model\Entity\BlogPost;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * BlogPostsServiceInterface
 */
interface BlogPostsServiceInterface
{

    /**
     * BlogPostsTable のファイルアップロードの設定を実施
     *
     * @param int $blogContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupUpload(int $blogContentId): void;

    /**
     * 単一データを取得する
     *
     * @param int $id
     * @param array $options
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, array $options = []);

    /**
     * ブログ記事一覧を取得する
     *
     * @param array $queryParams
     * @return \Cake\ORM\Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query;

    /**
     * 初期データ用のエンティティを取得
     *
     * @param int $userId
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(int $blogContentId, int $userId);

    /**
     * 新規登録
     *
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData);

    /**
     * ブログ記事を更新する
     *
     * @param EntityInterface|BlogPost $post
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $post, array $postData);

    /**
     * 公開状態を取得する
     *
     * @param EntityInterface $data モデルデータ
     * @return boolean 公開状態
     * @checked
     * @noTodo
     * @unitTest
     */
    public function allowPublish(EntityInterface $post);

    /**
     * コントロールソースを取得する
     *
     * blog_category_id / user_id  / blog_tag_id を対象とする
     *
     * @param string $field
     * @param array $options
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource(string $field, array $options = []);

    /**
     * 記事を公開状態に設定する
     *
     * 公開期間指定は初期化する
     *
     * @param int $id
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish(int $id);

    /**
     * 記事を非公開状態に設定する
     *
     * 公開期間指定は初期化する
     *
     * @param int $id
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish(int $id);

    /**
     * ブログ記事を削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool;

    /**
     * ブログ記事をコピーする
     *
     * @param int $id
     * @return false|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(int $id);

    /**
     * IDからタイトルリストを取得する
     *
     * @param array $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTitlesById(array $ids): array;

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(string $method, array $ids): bool;
    /**
     * カテゴリ別記事一覧を取得
     *
     * @param string $category
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexByCategory($category, array $options = []);

    /**
     * 著者別記事一覧を取得
     *
     * @param int $userId
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexByAuthor(int $userId, array $options = []);

    /**
     * タグ別記事一覧を取得
     *
     * @param string $tag
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexByTag(string $tag, array $options = []);
    /**
     * 日付別記事一覧を取得
     *
     * @param string $year
     * @param string $month
     * @param string $day
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexByDate(string $year, string $month, string $day, array $options = []);

    /**
     * 前の記事を取得する
     *
     * @param BlogPost $post ブログ記事
     * @return BlogPost|EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getPrevPost(BlogPost $post);
    /**
     * 次の記事を取得する
     *
     * @param BlogPost $post ブログ記事
     * @return BlogPost|EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNextPost(BlogPost $post);

    /**
     * 関連するブログ記事を取得する
     *
     * @param BlogPost $post
     * @param array $options
     * @return array|Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getRelatedPosts(BlogPost $post, $options = []);

}
