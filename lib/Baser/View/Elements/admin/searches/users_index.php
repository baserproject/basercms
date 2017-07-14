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
 * [ADMIN] ユーザー一覧　検索ボックス
 */
?>


<?php echo $this->BcForm->create('User', array('url' => array('action' => 'index'))) ?>
<p>
	<span><?php echo $this->BcForm->label('User.user_group_id', 'ユーザーグループ') ?></span>
	<span><?php echo $this->BcForm->input('User.user_group_id', array('type' => 'select', 'options' => $this->BcForm->getControlSource('User.user_group_id'), 'empty' => '指定なし')) ?></span>　
	<?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="button">
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>
<?php echo $this->Form->end() ?>