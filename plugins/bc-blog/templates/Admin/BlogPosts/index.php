<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [管理画面] ブログ記事 一覧
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setSearch('blog_posts_index');
$this->BcAdmin->setHelp('blog_posts_index');
$this->BcAdmin->setTitle(sprintf(
  __d('baser', '%s｜記事一覧'),
  strip_tags(
    $this->request->getAttribute('currentContent')->title
  )
));
$this->BcAdmin->addAdminMainBodyHeaderLinks([
  'url' => ['action' => 'add', $blogContent->id],
  'title' => __d('baser', '新規記事追加'),
]);
?>


<div id="AlertMessage" class="message" hidden></div>
<div id="MessageBox" hidden>
  <div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList" class="bca-data-list">
  <?php $this->BcBaser->element('BlogPosts/index_list') ?>
</div>
