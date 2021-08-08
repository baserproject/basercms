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
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
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
	<span class="bca-search__input-item">
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
<div class="button bca-search__btns">
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', '検索'), "javascript:void(0)", ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn', 'data-bca-btn-type' => 'search']) ?></div>
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', 'クリア'), "javascript:void(0)", ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?></div>
</div>
<?php echo $this->BcForm->end() ?>
