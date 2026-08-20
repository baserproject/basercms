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

/**
 * [ADMIN] ブログカテゴリ 一覧 セットアップ（JS/CSS 読込）
 * @var BcBlog\View\BlogAdminAppView $this
 * @var BcBlog\Model\Entity\BlogContent $blogContent
 * @var string $template
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->i18nScript([
  'blogCategoryConfirmDelete' => __d('baser_core', "カテゴリ「%s」を削除してもよろしいですか？\n※ このカテゴリに関連する記事は、どのカテゴリにも関連しない状態として残ります。"),
  'blogCategoryDeleteSuccess' => __d('baser_core', 'カテゴリを削除しました。'),
  'blogCategoryDeleteFailed' => __d('baser_core', '削除に失敗しました。'),
  'bcTreeCheck' => __d('baser_core', '確認'),
  'bcTreeEdit' => __d('baser_core', '編集'),
  'bcTreeAddChild' => __d('baser_core', '子カテゴリを追加'),
  'bcTreeDelete' => __d('baser_core', '削除'),
]);
$this->BcBaser->js('BcBlog.admin/blog_categories/index.bundle', false, [
  'id' => 'AdminBlogCategoriesIndexScript',
  'data-blogContentId' => $blogContent->id,
  'data-listType' => ($template === 'index_tree') ? 1 : 2,
  'data-addUrl' => $this->BcBaser->getUrl(['action' => 'add', $blogContent->id]),
]);
if ($template === 'index_tree') {
  $this->BcBaser->js(['vendor/jquery.jstree-3.3.8/jstree.min'], false);
  $this->BcBaser->css('../js/vendor/jquery.jstree-3.3.8/themes/proton/style.min', false);
}
