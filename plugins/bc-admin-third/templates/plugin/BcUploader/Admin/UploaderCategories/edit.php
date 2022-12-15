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
 * @var \BcUploader\Model\Entity\UploaderCategory $uploaderCategory
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', 'アップロードカテゴリ編集'));
?>


<?php echo $this->BcAdminForm->create($uploaderCategory) ?>
<?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('UploaderCategories/form') ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser', '更新'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' =>
      'save', 'data-bca-btn-size' =>
      'lg', 'data-bca-btn-width' => 'lg'
    ]) ?>
  </div>
  <div class="bca-actions__sub">
    <?php echo $this->BcAdminForm->postLink(__d('baser', '削除'),
      ['action' => 'delete', $this->BcAdminForm->getSourceValue('id')], [
        'confirm' => sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcAdminForm->getSourceValue('name')),
        'block' => true,
        'class' => 'bca-submit-token button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'sm'
      ]
    ) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
