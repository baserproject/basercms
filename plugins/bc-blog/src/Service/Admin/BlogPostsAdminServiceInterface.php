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

namespace BcBlog\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcBlog\Model\Entity\BlogPost;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;

/**
 * BlogPostsAdminServiceInterface
 */
interface BlogPostsAdminServiceInterface
{

    /**
     * 一覧用の View 変数を取得する
     * @param $posts
     * @param $request
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex($posts, $request);

    /**
     * 新規登録用の View変数を生成する
     * @param ServerRequest $request
     * @param EntityInterface $post
     * @param EntityInterface $user
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForAdd(ServerRequest $request, EntityInterface $post, EntityInterface $user): array;

    /**
     * 編集画面用の View変数を生成する
     *
     * @param ServerRequest $request
     * @param EntityInterface|BlogPost $post
     * @param User $user
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForEdit(ServerRequest $request, EntityInterface $post, EntityInterface $user): array;

    /**
     * 公開ページのリンクを取得する
     *
     * @param BlogPost $post
     * @return string
     * @checked
     * @noTodo
     */
    public function getPublishLink(BlogPost $post);

    /**
     * CKEditorのオプションを取得する
     *
     * @param bool $isDisableDraft
     * @return array|array[]|string[]
     * @checked
     * @noTodo
     */
    public function getEditorOptions(bool $isDisableDraft);
}
