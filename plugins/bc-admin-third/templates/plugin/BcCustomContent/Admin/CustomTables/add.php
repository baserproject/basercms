<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * カスタムテーブル新規登録
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $entity
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', ' テーブル新規登録'));
?>


<?php echo $this->BcAdminForm->create($entity) ?>

<?php $this->BcBaser->element('CustomTables/form') ?>

<div class="bca-actions">
  <div class="bca-actions__main">
    <?php $this->BcBaser->link(__d('baser', '一覧に戻る'),
      ['action' => 'index'], [
        'class' => 'button bca-btn',
        'data-bca-btn-type' => 'back-to-list'
      ]) ?>
    &nbsp;&nbsp;
    <?php echo $this->BcAdminForm->button(__d('baser', '保存'), [
      'type' => 'submit',
      'id' => 'BtnSave',
      'div' => false,
      'class' => 'bca-btn bca-actions__item bca-loading',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
