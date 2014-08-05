<?php
/**
 * [ADMIN] アップデート
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<div class="corner10 panel-box section">
	<?php echo $this->BcForm->create(array('action' => $this->request->action)) ?>
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
