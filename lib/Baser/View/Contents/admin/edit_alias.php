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
 * [ADMIN] 統合コンテンツ編集
 */
?>


<?php echo $this->BcForm->create('Content', ['url' => ['content_id' => $this->BcForm->value('Content.id')]]) ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcForm->input('Content.alias_id', ['type' => 'hidden']) ?>
<?php echo $this->BcForm->input('Content.site_id', ['type' => 'hidden']) ?>
<?php $this->BcBaser->element('admin/contents/form_alias') ?>
<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['class' => 'button', 'div' => false]) ?>
</div>

<?php echo $this->BcForm->end() ?>
