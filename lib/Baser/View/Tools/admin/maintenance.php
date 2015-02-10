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

<div class="submit"><?php $this->BcBaser->link('ダウンロード', array('backup'), array('class' => 'btn-red button')) ?> </div>

<h2>データの復元</h2>

<p>バックアップファイルをアップロードし、データベースのデータを復元します。<br />
	<small>ダウンロードしたバックアップファイルをZIPファイルのままアップロードします。<br />
		v1.6.6以前のバックアップデータの復元はできません。v1.6.6以前のデータを復元するには、phpMyAdminなどのDB管理ツールをご利用ください。</small></p>

<?php echo $this->BcForm->create('Tool', array('action' => 'maintenance', 'url' => array('restore'), 'type' => 'file')) ?>

<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
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