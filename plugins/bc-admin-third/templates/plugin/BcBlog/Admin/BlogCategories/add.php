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
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @var \BcBlog\Model\Entity\BlogCategory $blogCategory
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(
  __d('baser_core', '%s｜新規カテゴリ登録'),
  $blogContent->content->title
));
$this->BcAdmin->setHelp('blog_categories_form');
$this->BcBaser->js('BcBlog.admin/blog_categories/form.bundle', false);
?>


<?php echo $this->BcAdminForm->create($blogCategory, ['url' => [
  'controller' => 'blog_categories',
  'action' => 'add',
  $blogContent->id
], 'novalidate' => true]) ?>

<?php $this->BcBaser->element('BlogCategories/form') ?>

<div class="bca-actions">
  <div class="bca-actions__before">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), [
      'plugin' => 'BcBlog',
      'controller' => 'BlogCategories',
      'action' => 'index',
      $blogContent->id
    ], [
      'class' => 'bca-btn bca-actions__item',
      'data-bca-btn-type' => 'back-to-list'
    ]) ?>
  </div>
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
      'div' => false,
      'class' => 'bca-btn bca-actions__item bca-loading',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
