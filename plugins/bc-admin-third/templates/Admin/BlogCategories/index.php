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
 * [ADMIN] ブログカテゴリ 一覧
 * @var BcBlog\View\BlogAdminAppView $this
 * @var BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜カテゴリ一覧'), $blogContent->content->title));
$this->BcAdmin->setHelp('blog_categories_index');
$this->BcBaser->js('admin/blog_categories/index.bundle', false);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $blogContent->id],
  'title' => __d('baser', '新規追加'),
]);
?>


<div class="bca-data-list">
  <?php $this->BcBaser->element('BlogCategories/index_list') ?>
</div>
