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
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $post
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(__d('baser_core', '%s｜新規記事登録'), $this->getRequest()->getAttribute('currentContent')->title));
$this->BcAdmin->setHelp('blog_posts_form');
?>


<?php echo $this->BcAdminForm->create($post, ['type' => 'file', 'url' => ['controller' => 'blog_posts', 'action' => 'add', $blogContent->id], 'id' => 'BlogPostForm']) ?>

<?php $this->BcBaser->element('BlogPosts/form') ?>

<section class="bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', 'プレビュー'),
      [
        'id' => 'BtnPreview',
        'div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'preview',
      ]) ?>
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'),
      [
        'type' => 'submit',
        'id' => 'BtnSave',
        'div' => false,
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
      ]) ?>
  </div>
</section>

<?php echo $this->BcAdminForm->end() ?>

<?php $this->BcBaser->element('BlogPosts/popup_blog_category') ?>
