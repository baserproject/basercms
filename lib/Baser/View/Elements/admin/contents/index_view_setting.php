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
if ($this->action == 'admin_index') {
	echo $this->BcForm->hidden('ViewSetting.mode', ['value' => 'index']);
} elseif ($this->action = 'admin_trash_index') {
	echo $this->BcForm->hidden('ViewSetting.mode', ['value' => 'trash']);
}
?>


<?php if ($this->action == 'admin_index'): ?>
	<div class="panel-box" id="ViewSetting">
		<?php if (count($sites) >= 2): ?>
			<small><?php echo __d('baser', 'サイト') ?></small> <?php echo $this->BcForm->input('ViewSetting.site_id', ['type' => 'select', 'options' => $sites]) ?>　｜　
		<?php else : ?>
			<?php echo $this->BcForm->input('ViewSetting.site_id', ['type' => 'hidden', 'options' => $sites]) ?>
		<?php endif ?>
		<small><?php echo __d('baser', '表示設定') ?></small> <?php echo $this->BcForm->input('ViewSetting.list_type', ['type' => 'radio', 'options' => $listTypes]) ?>
		<span id="GrpChangeTreeOpenClose">
        　｜　
        <button id="BtnOpenTree" class="button-small"><?php echo __d('baser', '全て展開') ?></button>　
        <button id="BtnCloseTree" class="button-small"><?php echo __d('baser', '全て閉じる') ?></button>
    </span>
	</div>
<?php endif ?>
