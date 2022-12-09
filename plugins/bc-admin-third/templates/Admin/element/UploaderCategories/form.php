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
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<table class="list-table bca-form-table" id="ListTable">
  <?php if ($this->getRequest()->getParam('action') === 'admin_edit'): ?>
    <tr>
      <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('id', 'No') ?></th>
      <td class="bca-form-table__input">
        <?php echo $this->BcAdminForm->getSourceValue('id') ?>
      </td>
    </tr>
  <?php endif; ?>
  <tr>
    <th class="bca-form-table__label">
      <?php echo $this->BcAdminForm->label('name', __d('baser', 'カテゴリ名')) ?>
      &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
    </th>
    <td class="bca-form-table__input">
      <?php echo $this->BcAdminForm->control('name', [
        'type' => 'text',
        'size' => 40,
        'maxlength' => 50,
        'autofocus' => true
      ]) ?>
      <?php echo $this->BcAdminForm->error('name') ?>
    </td>
  </tr>

  <?php echo $this->BcAdminForm->dispatchAfterForm() ?>

</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>
