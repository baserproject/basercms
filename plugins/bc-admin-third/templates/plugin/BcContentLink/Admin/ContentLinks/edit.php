<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */
/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\ContentLink $contentLink
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', 'リンク編集'));
?>


<?php echo $this->BcAdminForm->create($contentLink) ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcAdminForm->hidden('id') ?>

<table class="form-table bca-form-table">
  <tr>
    <th class="bca-form-table__label"><?php echo __d('baser_core', 'リンク先URL') ?></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('url', ['type' => 'text', 'size' => 60, 'placeholder' => 'httpssrc/Controller/ContentLinksController://']) ?>
      <br>
      <?php echo $this->BcAdminForm->error('url') ?>
    </td>
  </tr>
  <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
  <?php echo $this->BcAdminForm->submit(__d('baser_core', '保存'), ['class' => 'button bca-btn',
    'data-bca-btn-type' => 'save',
    'data-bca-btn-size' => 'lg',
    'data-bca-btn-width' => 'lg',
    'div' => false
  ]) ?>
</div>
<?php echo $this->BcAdminForm->end() ?>
