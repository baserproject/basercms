<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] スキーマ読み込み フォーム
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?>
</h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>スキーマファイルの読み込みテストを行えます。</p>
	<p>※ 単一ファイルのみ対応</p>
</div>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<!-- form -->
<?php echo $formEx->create('Tool', array('action' => 'load_schema', 'type' => 'file')) ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Tool.schema_type', 'スキーマタイプ') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Tool.schema_type', array(
					'type' => 'radio',
					'options' => array('create'=>'テーブル作成', 'alter'=>'テーブル構造変更', 'drop' => 'テーブル削除'),
					'legend' => false,
					'separator' => '　')) ?>
			<?php echo $formEx->error('Tool.schema_type') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Tool.schema_file', 'スキーマファイル') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Tool.schema_file', array('type' => 'file')) ?>
			<?php echo $formEx->error('Tool.schema_file') ?>
		</td>
	</tr>
</table>

<div class="align-center"><?php echo $formEx->submit('読み込み', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $formEx->end() ?>
