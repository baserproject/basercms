<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] アップデート
 */
?>


<div class="corner10 panel-box section">
	<?php echo $this->BcForm->create('Updater', ['url' => ['action' => $this->request->action]]) ?>
	<p><?php echo $this->BcForm->label('Updater.plugin', __d('baser', 'タイプ')) ?>
		&nbsp;<?php echo $this->BcForm->input('Updater.plugin', ['type' => 'select', 'options' => $plugins, 'empty' => __d('baser', 'コア')]) ?></p>
	<p><?php echo $this->BcForm->label('Updater.version', __d('baser', 'バージョン')) ?>
		&nbsp;<?php echo $this->BcForm->input('Updater.version', ['type' => 'text']) ?></p>
	<?php echo $this->BcForm->end(['label' => __d('baser', '実行'), 'class' => 'button btn-red']) ?>
</div>


<?php if ($log): ?>
	<div class="corner10 panel-box section" id="UpdateLog">
		<h2><?php echo __d('baser', 'アップデートログ') ?></h2>
		<?php echo $this->BcForm->textarea('Updater.log', [
			'value' => $log,
			'style' => 'width:99%;height:200px;font-size:12px',
			'readonly' => 'readonly'
		]); ?>
	</div>
<?php endif; ?>
