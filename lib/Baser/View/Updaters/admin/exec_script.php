<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] アップデート
 */
?>


<div class="corner10 panel-box section">
	<?php echo $this->BcForm->create('Updater', ['url' => ['action' => $this->request->action]]) ?>
	<p><?php echo $this->BcForm->label('Updater.plugin', 'タイプ') ?>&nbsp;<?php echo $this->BcForm->input('Updater.plugin', array('type' => 'select', 'options' => $plugins, 'empty' => 'コア')) ?></p>
	<p><?php echo $this->BcForm->label('Updater.version', 'バージョン') ?>&nbsp;<?php echo $this->BcForm->input('Updater.version', array('type' => 'text')) ?></p>
	<?php echo $this->BcForm->end(array('label' => '実行', 'class' => 'button btn-red')) ?>
</div>


<?php if ($log): ?>
	<div class="corner10 panel-box section" id="UpdateLog">
		<h2>アップデートログ</h2>
		<?php echo $this->BcForm->textarea('Updater.log', array(
			'value' => $log,
			'style' => 'width:99%;height:200px;font-size:12px',
			'readonly' => 'readonly'
		)); ?>
	</div>
<?php endif; ?>
