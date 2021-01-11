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
<p>
	<span><?php echo $this->BcForm->label('User.user_group_id', __d('baser', 'ユーザーグループ')) ?></span>
	<span><?php echo $this->BcForm->input('User.user_group_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('User.user_group_id'), 'empty' => __d('baser', '指定なし')]) ?></span>　
	<?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="submit">
	<?php echo $this->BcForm->button(__d('baser', '検索'), ['class' => 'button', 'id' => 'BtnSearchSubmit']) ?>
	<?php echo $this->BcForm->button(__d('baser', 'クリア'), ['class' => 'button', 'id' => 'BtnSearchClear']) ?>
</div>
<?php echo $this->Form->end() ?>
