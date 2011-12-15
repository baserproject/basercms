<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] データメンテナンス
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<h2>
	<?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?>
</h2>

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>データベースのバックアップと復元が行えますので、定期的にバックアップを保存しておく事をおすすめします。</p>
	<ul><li>データベースのデータと構造をバックアップします。</li>
		<li>baserCMSのバージョンが違う場合は復元する事ができない場合があります。</li>
		<li>環境によっては復元に失敗する可能性もあります。バックアップと復元は必ず自己責任で行ってください。<br />
			<small>※ 運用を開始する前に、バックアップと復元が正常に動作するかの確認をおすすめします。</small></li>
	</ul>
</div>

<h3>データのバックアップ</h3>

<p>データベースのデータをバックアップファイルとしてPCにダウンロードします。</p>

<div class="align-center"><?php $baser->link('ダウンロード', array('backup'), array('class' => 'btn-red button')) ?> </div>

<h3>データの復元</h3>

<p>バックアップファイルをアップロードし、データベースのデータを復元します。<br />
<small>ダウンロードしたバックアップファイルをZIPファイルのままアップロードします。<br />
v1.6.6以前のバックアップデータの復元はできません。v1.6.6以前のデータを復元するには、phpMyAdminなどのDB管理ツールをご利用ください。</small></p>

<?php echo $formEx->create('Tool', array('action' => 'maintenance', 'url' => array('restore'), 'type' => 'file')) ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Tool.backup', 'バックアップファイル') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Tool.backup', array('type' => 'file')) ?>
			<?php echo $formEx->error('Tool.backup') ?>
		</td>
	</tr>
</table>

<div class="align-center"><?php echo $formEx->submit('アップロード', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $formEx->end() ?>