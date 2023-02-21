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
 * カスタムフィールド編集
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomField $entity
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', '新規フィールド追加'));
?>


<?php echo $this->BcAdminForm->create($entity, ['novalidate' => true]) ?>

<?php $this->BcBaser->element('CustomFields/form') ?>

<div class="bca-actions">
  <div class="bca-actions__main">
    <?php $this->BcBaser->link(__d('baser', '一覧に戻る'),
      ['action' => 'index'], [
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
