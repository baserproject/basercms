<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] プラグイン　フォーム
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
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>プラグイン設定の登録・変更を行います。<br />
		新しいプラグインを入手した場合は、そのままの内容で登録しても大丈夫です。
		プラグイン一覧の「管理」ボタンをクリックした際に表示するページを変更する場合は「管理URL」を変更します。</p>
</div>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $formEx->create('Plugin',array('url'=>array($this->data['Plugin']['name']))) ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Plugin.id', 'NO') ?></th>
		<td class="col-input"><?php echo $formEx->text('Plugin.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp; </td>
	</tr>
	<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Plugin.name', 'プラグイン名') ?></th>
		<td class="col-input"><?php echo $formEx->text('Plugin.name', array('size'=>40,'maxlength'=>255,'readonly'=>'readonly')) ?> <?php echo $formEx->error('Plugin.name') ?>&nbsp;</td>
	</tr>
</table>
<div class="submit">
	<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
	<?php else: ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $formEx->value('Plugin.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('Plugin.name')),false); ?>
	<?php endif ?>
</div>
