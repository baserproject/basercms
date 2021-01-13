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
 * [ADMIN] データメンテナンス
 */
?>
<script>
	$(function () {
		$("#BtnUpload").click(function () {
			$.bcUtil.showLoader();
		});
	});
</script>

<div class="section">
	<h2><?php echo __d('baser', 'データのバックアップ') ?></h2>
	<p><?php echo __d('baser', 'データベースのデータをバックアップファイルとしてPCにダウンロードします。') ?></p>
	<?php echo $this->BcForm->create('Tool', ['type' => 'get', 'url' => ['action' => 'maintenance', 'backup'], 'target' => '_blank']) ?>
	<p>
		<?php echo $this->BcForm->input('Tool.backup_encoding', ['type' => 'radio', 'options' => ['UTF-8' => 'UTF-8', 'SJIS-win' => 'SJIS'], 'value' => 'UTF-8']) ?>
		<?php echo $this->BcForm->error('Tool.backup_encoding') ?>
	</p>
	<p><?php echo $this->BcForm->submit(__d('baser', 'ダウンロード'), ['div' => false, 'class' => 'button-small', 'id' => 'BtnDownload']) ?></p>
	<?php echo $this->BcForm->end() ?>
</div>

<div class="section">
	<h2><?php echo __d('baser', 'データの復元') ?></h2>
	<p><?php echo __d('baser', 'バックアップファイルをアップロードし、データベースのデータを復元します。') ?><br/>
		<small><?php echo __d('baser', 'ダウンロードしたバックアップファイルをZIPファイルのままアップロードします。') ?></small></p>
	<?php echo $this->BcForm->create('Tool', ['url' => ['action' => 'maintenance', 'restore'], 'type' => 'file']) ?>
	<p><?php echo $this->BcForm->input('Tool.encoding', ['type' => 'radio', 'options' => ['auto' => __d('baser', '自動判別'), 'UTF-8' => 'UTF-8', 'SJIS-win' => 'SJIS'], 'value' => 'auto']) ?>
		<?php echo $this->BcForm->error('Tool.encoding') ?></p>
	<p><?php echo $this->BcForm->input('Tool.backup', ['type' => 'file']) ?>
		<?php echo $this->BcForm->error('Tool.backup') ?></p>
	<p><?php echo $this->BcForm->submit(__d('baser', 'アップロード'), ['div' => false, 'class' => 'button-small', 'id' => 'BtnUpload']) ?></p>
	<?php echo $this->BcForm->end() ?>
</div>
