<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] アクセス制限管理（ポップアップ）
 */
?>


<div id="PermissionDialog" title="アクセス制限登録" style="display:none">
	<?php echo $this->BcForm->create('Permission', array('url' => array('plugin' => null, 'action' => 'ajax_add'))) ?>
	<?php echo $this->BcForm->input('Permission.id') ?>
	<dl>
		<dt><?php echo $this->BcForm->label('Permission.user_group_id', 'ユーザーグループ') ?></dt>
		<dd class="col-input">
			<?php echo $this->BcForm->input('Permission.user_group_id', array('type' => 'select', 'options' => $this->BcForm->getControlSource('Permission.user_group_id'))) ?>
		</dd>
		<dt><h4><?php echo $this->BcForm->label('Permission.name', 'ルール名') ?></h4></dt>
		<dd><?php echo $this->BcForm->input('Permission.name', array('type' => 'text', 'size' => 30, 'class' => 'required')) ?></dd>
		<dt><?php echo $this->BcForm->label('Permission.url', 'URL設定') ?></dt>
		<dd><strong id="PermissionAdmin">/<?php echo Configure::read('Routing.prefixes.0') ?>/</strong><?php echo $this->BcForm->input('Permission.url', array('type' => 'text', 'size' => 30, 'class' => 'required')) ?></dd>
		<dt><?php echo $this->BcForm->label('Permission.auth', 'アクセス') ?></dt>
		<dd>
			<?php
			echo $this->BcForm->input('Permission.auth', array(
				'type' => 'radio',
				'options' => $this->BcForm->getControlSource('Permission.auth'),
				'legend' => false,
				'value' => 0,
				'separator' => '　'))
			?>
	<?php echo $this->BcForm->error('Permission.auth') ?>
		</dd>
	</dl>
<?php echo $this->BcForm->end() ?>
</div>