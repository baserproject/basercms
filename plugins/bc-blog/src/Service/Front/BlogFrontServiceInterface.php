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

namespace BcBlog\Service\Front;

use BcBlog\Model\Entity\BlogContent;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\ORM\ResultSet;

/**
 * BlogFrontServiceInterface
 */
interface BlogFrontServiceInterface
{

    /**
     * プレビュー用の view 変数を取得する
     *
     * @param ServerRequest $request
     * @return array[]
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(ServerRequest $request, BlogContent $blogContent, ResultSet $posts): array;

    /**
     * プレビュー用のセットアップをする
     *
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForIndex(Controller $controller): void;

    /**
     * カテゴリー別アーカイブ一覧の view 変数を取得する
     *
     * @param ResultSet $posts
     * @param string $category
     * @param ServerRequest $request
     * @param EntityInterface $blogContent
     * @param array $crumbs
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByCategory(
        ResultSet       $posts,
        string          $category,
        ServerRequest   $request,
        EntityInterface $blogContent,
        array           $crumbs
    ): array;

    /**
     * カテゴリ用のパンくずを取得する
     *
     * @param string $baseUrl
     * @param int $categoryId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCategoryCrumbs(string $baseUrl, int $categoryId, $isCategoryPage = true): array;

    /**
     * 著者別アーカイブ一覧の view 用変数を取得する
     * @param ResultSet $posts
     * @param string $author
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByAuthor(ResultSet $posts, string $author, BlogContent $blogContent): array;

    /**
     * タグ別アーカイブ一覧の view 用変数を取得する
     *
     * @param ResultSet $posts
     * @param string $tag
     * @param BlogContent $blogContent
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByTag(ResultSet $posts, string $tag, BlogContent $blogContent): array;

    /**
     * 日付別アーカイブ一覧の view 用変数を取得する
     *
     * @param ResultSet $posts
     * @param string $year
     * @param string $month
     * @param string $day
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByDate(ResultSet $posts, string $year, string $month, string $day, BlogContent $blogContent): array;

    /**
     * ブログ記事詳細ページの view 用変数を取得する
     *
     * @param ServerRequest $request
     * @param EntityInterface $blogContent
     * @param array $crumbs
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForSingle(ServerRequest $request, EntityInterface $blogContent, array $crumbs): array;

    /**
     * プレビュー用のセットアップをする
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForArchives(Controller $controller): void;

    /**
     * 一覧用のテンプレート名を取得する
     * @param BlogContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexTemplate(BlogContent $blogContent): string;

    /**
     * アーカイブページ用のテンプレート名を取得する
     *
     * @param BlogContent $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getArchivesTemplate(BlogContent $blogContent): string;

    /**
     * 詳細ページ用のテンプレート名を取得する
     *
     * @param BlogContent $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSingleTemplate(BlogContent $blogContent): string;

}
