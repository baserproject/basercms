<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログタグ フォーム
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js('admin/blog_tags/form.bundle', false);
?>


<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <?php if ($this->getRequest()->getParam('action') === 'edit'): ?>
      <tr>
        <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('id', __d('baser', 'No')) ?></th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->getSourceValue('id') ?>
          <?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>
        </td>
      </tr>
    <?php endif; ?>
    <tr>
      <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser', 'ブログタグ名')) ?>
        &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
        <?php echo $this->BcAdminForm->error('name') ?>
      </td>
    </tr>

    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>
