<?php
/**
 * [ADMIN] データメンテナンス
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


<h2>データのバックアップ</h2>

<p>データベースのデータをバックアップファイルとしてPCにダウンロードします。</p>

<?php echo $this->BcForm->create('Tool', array('type' => 'get', 'url' => array('action' => 'maintenance', 'backup'))) ?>

	<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
		<tr>
			<th class="col-head" style="width:200px"><span class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.encoding', '文字コード') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Tool.backup_encoding', array('type' => 'radio', 'options' => array('UTF-8' => 'UTF-8', 'SJIS-win' => 'SJIS'), 'value' => 'UTF-8')) ?>
				<?php echo $this->BcForm->error('Tool.backup_encoding') ?>
			</td>
		</tr>
	</table>

	<div class="submit"><?php echo $this->BcForm->submit('ダウンロード', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $this->BcForm->end() ?>

<h2>データの復元</h2>

<p>バックアップファイルをアップロードし、データベースのデータを復元します。<br />
	<small>ダウンロードしたバックアップファイルをZIPファイルのままアップロードします。<br />
		v1.6.6以前のバックアップデータの復元はできません。v1.6.6以前のデータを復元するには、phpMyAdminなどのDB管理ツールをご利用ください。</small></p>

<?php echo $this->BcForm->create('Tool', array('action' => 'maintenance', 'url' => array('restore'), 'type' => 'file')) ?>

<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<tr>
		<th class="col-head" style="width:200px"><span class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.encoding', '文字コード') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.encoding', array('type' => 'radio', 'options' => array('auto' => '自動判別', 'UTF-8' => 'UTF-8', 'SJIS-win' => 'SJIS'), 'value' => 'auto')) ?>
			<?php echo $this->BcForm->error('Tool.encoding') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.backup', 'バックアップファイル') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.backup', array('type' => 'file')) ?>
			<?php echo $this->BcForm->error('Tool.backup') ?>
		</td>
	</tr>
</table>

<div class="submit"><?php echo $this->BcForm->submit('アップロード', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $this->BcForm->end() ?>