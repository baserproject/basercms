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
 * @var \BcBlog\Model\Entity\BlogTag $blogTag
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', 'タグ編集'));
?>


<!-- form -->
<?php echo $this->BcAdminForm->create($blogTag) ?>

<?php $this->BcBaser->element('BlogTags/form') ?>

<!-- button -->
<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser', '保存'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'id' => 'BtnSave',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg'
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->BcAdminForm->getSourceValue('BlogTag.id')], [
      'class' => 'bca-submit-token button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'delete',
      'data-bca-btn-size' => 'sm'
    ], sprintf(__d('baser', "%s を本当に削除してもいいですか？\nこのタグに関連する記事は削除されません。"), $this->BcAdminForm->getSourceValue('BlogTag.name'))); ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
