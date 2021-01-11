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
 * [ADMIN] ユーザー一覧　検索ボックス
 */
?>


<?php echo $this->BcForm->create('User', ['url' => ['action' => 'index']]) ?>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcForm->label('User.user_group_id', __d('baser', 'ユーザーグループ'), ['class' => 'bca-search__input-item-label']) ?>
		<?php echo $this->BcForm->input('User.user_group_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('User.user_group_id'), 'empty' => __d('baser', '指定なし')]) ?>
	</span>
</p>
<div class="button bca-search__btns">
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', '検索'), "javascript:void(0)", ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn', 'data-bca-btn-type' => 'search']) ?></div>
	<div
		class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', 'クリア'), "javascript:void(0)", ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?></div>
</div>
<?php echo $this->Form->end() ?>
