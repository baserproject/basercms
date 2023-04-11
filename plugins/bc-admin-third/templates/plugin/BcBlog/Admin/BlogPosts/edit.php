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
 * @var string $fullUrl
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(sprintf(__d('baser_core', '%s｜記事編集'), $this->getRequest()->getAttribute('currentContent')->title));
$this->BcAdmin->setHelp('blog_posts_form');
?>


<?php echo $this->BcAdminForm->create($post, ['type' => 'file', 'url' => ['controller' => 'blog_posts', 'action' => 'edit', $blogContent->id, $post->id, 'id' => false], 'id' => 'BlogPostForm']) ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<div class="bca-section bca-section__post-top">
  <span class="bca-post__no">
    <?php echo $this->BcAdminForm->label('no', 'No') ?> : <strong><?php echo $post->no ?></strong>
    <?php echo $this->BcAdminForm->control('no', ['type' => 'hidden']) ?>
  </span>
  <span class="bca-post__url">
    <a href="<?php echo $fullUrl ?>"
       class="bca-text-url" target="_blank" data-toggle="tooltip" data-placement="top" title="<?php echo __d('baser_core', '公開URLを開きます') ?>">
      <i class="bca-icon--globe"></i>
      <?php echo $fullUrl ?>
    </a>
    <?php echo $this->BcAdminForm->button('', [
      'id' => 'BtnCopyUrl',
      'class' => 'bca-btn',
      'type' => 'button',
      'data-bca-btn-type' => 'textcopy',
      'data-bca-btn-category' => 'text',
      'data-bca-btn-size' => 'sm'
    ]) ?>
  </span>
</div>

<?php $this->BcBaser->element('BlogPosts/form') ?>

<!-- button -->
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
  <div class="bca-actions__sub">
      <?= $this->BcAdminForm->postLink(
        __d('baser_core', '削除'),
        ['action' => 'delete', $blogContent->id, $post->id],
        ['block' => true,
          'confirm' => __d('baser_core', "{0} を本当に削除してもいいですか？\n\nブログ記事はゴミ箱に入らず完全に消去されます。", $post->name),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm']
      ) ?>
  </div>
</section>

<?php echo $this->BcAdminForm->end() ?>

<?php $this->BcBaser->element('BlogPosts/popup_blog_category') ?>

<?= $this->fetch('postLink') ?>
