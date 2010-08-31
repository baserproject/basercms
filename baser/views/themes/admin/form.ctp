<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] テーマ フォーム
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
	<p>テーマ情報の編集が行えます。編集内容は、テーマフォルダ名と、テーマ設定ファイルに反映されます。<br />
		<small>テーマフォルダ：<?php echo WWW_ROOT.'themed'.DS.$theme.DS ?></small><br />
		<small>テーマ設定ファイル：<?php echo WWW_ROOT.'themed'.DS.$theme.DS.'config.php' ?></small>
	</p>
</div>
<?php if($folderDisabled): ?>
<p><span class="required">テーマフォルダに書込権限がありません。</span></p>
<?php endif ?>
<?php if($configDisabled): ?>
<p><span class="required">テーマ設定ファイルに書込権限がありません。</span></p>
<?php endif ?>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $form->create('Theme',array('action'=>'edit','url'=>array('action'=>'edit',$theme))) ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('Theme.name', 'テーマ名') ?></th>
		<td class="col-input"><?php echo $form->text('Theme.name', array('size'=>20,'maxlength'=>255,'disabled'=>$folderDisabled)) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextName" class="helptext"> 半角のみで入力してください。 </div>
			<?php echo $form->error('Theme.name') ?>&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('Theme.title', 'タイトル') ?></th>
		<td class="col-input"><?php echo $form->text('Theme.title', array('size'=>20,'maxlength'=>255,'disabled'=>$configDisabled)) ?>
			<?php echo $form->error('Theme.title') ?>&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('Theme.description', '説明') ?></th>
		<td class="col-input"><?php echo $form->textarea('Theme.description', array('rows'=>5,'cols'=>60,'disabled'=>$configDisabled)) ?>
			<?php echo $form->error('Theme.description') ?>&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('Theme.author', '制作者') ?></th>
		<td class="col-input"><?php echo $form->text('Theme.author', array('size'=>20,'maxlength'=>255,'disabled'=>$configDisabled)) ?>
			<?php echo $form->error('Theme.author') ?>&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('Theme.url', 'URL') ?></th>
		<td class="col-input"><?php echo $form->text('Theme.url', array('size'=>20,'maxlength'=>255,'disabled'=>$configDisabled)) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpUrl','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextUrl" class="helptext">
				<ul>
					<li>制作者のWEBサイトのURL。</li>
					<li>半角のみで入力してください。</li>
				</ul>
			</div>
			<?php echo $form->error('Theme.url') ?>&nbsp; </td>
	</tr>
</table>
<?php if(!$folderDisabled): ?>
<div class="align-center">
	<?php echo $form->submit('更　新',array('div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除', array('action'=>'del', $form->value('Theme.name')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('Theme.name')),false); ?>
	</form>
</div>
<?php endif ?>
