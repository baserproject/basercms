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
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $customTable
 * @var \BcCustomContent\Model\Entity\CustomEntry $entity
 * @var int $tableId
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', '{0}｜新規エントリー登録', $customTable->title));
?>

<?php echo $this->BcAdminForm->create($entity, ['type' => 'file', 'novalidate' => true]) ?>
<?php echo $this->BcAdminForm->control('custom_table_id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('CustomEntries/form') ?>


<div class="bca-actions">
  <div class="bca-actions__main">
    <?php $this->BcBaser->link(__d('baser', '一覧に戻る'),
      ['action' => 'index', $tableId], [
        'class' => 'button bca-btn',
        'data-bca-btn-type' => 'back-to-list'
      ]) ?>&nbsp;&nbsp;
    <?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
      'div' => false,
      'class' => 'bca-btn bca-loading',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'id' => 'BtnSave'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end(); ?>
