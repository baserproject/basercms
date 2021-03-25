<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] アクセス制限管理（ポップアップ）
 */
?>


<div id="PermissionDialog" title="アクセス制限登録" style="display:none">
	<?php echo $this->BcForm->create('Permission', ['url' => ['plugin' => null, 'action' => 'ajax_add']]) ?>
	<?php echo $this->BcForm->input('Permission.id') ?>
	<dl>
		<dt><?php echo $this->BcForm->label('Permission.user_group_id', __d('baser', 'ユーザーグループ')) ?></dt>
		<dd class="col-input">
			<?php echo $this->BcForm->input('Permission.user_group_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('Permission.user_group_id')]) ?>
		</dd>
		<dt><?php echo $this->BcForm->label('Permission.name', __d('baser', 'ルール名')) ?></dt>
		<dd><?php echo $this->BcForm->input('Permission.name', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
		<dt><?php echo $this->BcForm->label('Permission.url', __d('baser', 'URL設定')) ?></dt>
		<dd><strong id="PermissionAdmin"><?php echo '/' . Configure::read('Routing.prefixes.0') . '/' ?></strong>
			<?php echo $this->BcForm->input('Permission.url', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?>
		</dd>
		<dt><?php echo $this->BcForm->label('Permission.auth', __d('baser', 'アクセス')) ?></dt>
		<dd>
			<?php
			echo $this->BcForm->input('Permission.auth', [
				'type' => 'radio',
				'options' => $this->BcForm->getControlSource('Permission.auth'),
				'legend' => false,
				'value' => 0,
				'separator' => '　'])
			?>
			<?php echo $this->BcForm->error('Permission.auth') ?>
		</dd>
	</dl>
	<?php echo $this->BcForm->end() ?>
</div>
