<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] エイリアス編集
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Content $content
 */
$this->BcAdmin->setTitle(__d('baser', 'エイリアス編集'));
?>


<?php echo $this->BcAdminForm->create($content) ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcAdminForm->control('content.alias_id', ['type' => 'hidden']) ?>
<?php echo $this->BcAdminForm->control('content.site_id', ['type' => 'hidden']) ?>

<?php $this->BcBaser->element('Contents/form_alias') ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
  <?php echo $this->BcAdminForm->submit(__d('baser', '保存'), [
    'class' => 'bca-btn',
    'data-bca-btn-type' => 'save',
    'data-bca-btn-size' => 'lg',
    'data-bca-btn-width' => 'lg',
    'id' => 'BtnSave',
    'div' => false
  ]) ?>
</div>

<?php echo $this->BcAdminForm->end() ?>
