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
 * @var string $template 表示テンプレート（index_list: 表形式 / index_tree: ツリー形式）
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(__d('baser_core', '%s｜カテゴリ一覧'), $blogContent->content->title));
$this->BcAdmin->setHelp('blog_categories_index');
$this->BcBaser->element('BlogCategories/index_setup', ['blogContent' => $blogContent, 'template' => $template]);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $blogContent->id],
  'title' => __d('baser_core', '新規追加'),
]);
?>


<div id="AlertMessage" class="message" style="display:none"></div>

<?php $this->BcBaser->element('BlogCategories/index_view_setting') ?>

<div class="bca-data-list">
  <?php $this->BcBaser->element('BlogCategories/' . $template) ?>
</div>
