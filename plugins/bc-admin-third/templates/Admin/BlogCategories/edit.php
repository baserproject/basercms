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
  __d('baser', '%s｜カテゴリ編集'),
  $blogContent->content->title
));
$this->BcAdmin->setHelp('blog_categories_form');
$this->BcBaser->js('BcBlog.admin/blog_categories/form.bundle', false);
$fullUrl = $this->BcBaser->getContentsUrl(
    $blogContent->content->url,
    true,
    $blogContent->content->site->use_subdomain
  ) . 'archives/category/' . $blogCategory->name;
?>


<div class="bca-section bca-section__post-top">
	<span class="bca-post__no">
		<?php echo $this->BcAdminForm->label('BlogCategory.no', 'No') ?> :
		<strong><?php echo h($blogCategory->no) ?></strong>
		<?php echo $this->BcAdminForm->control('BlogCategory.no', ['type' => 'hidden']) ?>
	</span>
  <span class="bca-post__url">
	  <a href="<?php echo $this->BcBaser->getUri($fullUrl) ?>"
       class="bca-text-url"
       target="_blank"
       data-toggle="tooltip"
       data-placement="top" title="公開URLを開きます">
	    <i class="bca-icon--globe"></i>
	    <?php echo $this->BcBaser->getUri($fullUrl) ?>
	  </a>
	  <?php echo $this->BcAdminForm->button('', [
      'id' => 'BtnCopyUrl',
      'class' => 'bca-btn',
      'type' => 'button',
      'data-bca-btn-type' => 'textcopy',
      'data-bca-btn-category' => 'text',
      'data-bca-btn-size' => 'sm'
    ]) ?>
</div>

<?php echo $this->BcAdminForm->create($blogCategory, ['url' => [
  'controller' => 'blog_categories',
  'action' => 'edit', $blogContent->id,
  $blogCategory->id
], 'novalidate' => true]) ?>

<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('BlogCategories/form') ?>

<div class="bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser', '保存'), [
      'div' => false,
      'class' => 'bca-btn bca-actions__item bca-loading',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg'
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?php echo $this->BcAdminForm->postLink(__d('baser', '削除'),
      ['action' => 'delete', $blogContent->id, $blogCategory->id],
      [
        'block' => true,
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'sm',
        'confirm' => sprintf("%s を本当に削除してもいいですか？\nこのカテゴリに関連する記事は、どのカテゴリにも関連しない状態として残ります。", $blogCategory->name)
      ]
    ) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
