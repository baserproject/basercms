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
 * [ADMIN] ブログ記事コメント 一覧
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $blogPost
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setHelp('blog_comments_index');
if ($blogPost) {
    $this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜コメント一覧'), $blogPost->title));
} else {
    $this->BcAdmin->setTitle(sprintf(__d('baser', '%s｜コメント一覧'), $blogContent->content->title));
}
?>


<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
  <div id="flashMessage" class="notice-message"></div>
</div>

<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('BlogComments/index_list') ?></div>
