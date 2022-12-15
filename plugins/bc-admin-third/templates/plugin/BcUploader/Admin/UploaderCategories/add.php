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
$this->BcAdmin->setTitle(__d('baser', 'アップロードカテゴリ新規登録'));
?>


<?php echo $this->BcAdminForm->create($uploaderCategory) ?>

<?php $this->BcBaser->element('UploaderCategories/form') ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser', '登録'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
