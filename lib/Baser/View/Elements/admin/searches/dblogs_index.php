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
 * コンテンツ一覧
 */
?>
<?php echo $this->BcForm->create(
	'Dblogs',
	[
		'url' => ['action' => 'index']
	]
) ?>
<?php echo $this->BcForm->hidden(
	'Dblogs.open',
	['value' => true]
) ?>
<p>
	<span>
		<?php echo $this->BcForm->label(
			'Dblogs.name',
			__d('baser', 'キーワード')
		);
		?>
		<?php echo $this->BcForm->input(
			'Dblogs.name',
			[
				'type' => 'text',
				'size' => 20
			]
		);
		?>
	</span>
	<span>
		<?php echo $this->BcForm->label(
			'Dblogs.author_id',
			__d('baser', 'ユーザー')
		);
		?>
		<?php echo $this->BcForm->input(
			'Dblogs.user_id',
			[
				'type' => 'select',
				'options' => $this->get('userList'),
				'empty' => __d('baser', '指定しない')
			]
		);
		?>　
	</span>
	<?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="submit">
	<?php
		echo $this->BcForm->button(
			__d('baser', '検索'),
			[
				'class' => 'button',
				'id' => 'BtnSearchSubmit'
			]
		);
	?>
</div>
<?php echo $this->BcForm->end() ?>
