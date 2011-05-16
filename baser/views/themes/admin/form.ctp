<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマ フォーム
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
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

<!-- help -->
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

<?php echo $formEx->create('Theme', array('action' => 'edit', 'url' => array('action' => 'edit', $theme))) ?>

<!-- form -->
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Theme.name', 'テーマ名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Theme.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $folderDisabled)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('Theme.name') ?>
			<div id="helptextName" class="helptext"> 半角のみで入力してください。 </div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Theme.title', 'タイトル') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Theme.title', array('type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $configDisabled)) ?>
			<?php echo $formEx->error('Theme.title') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Theme.description', '説明') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Theme.description', array('type' => 'textarea', 'rows' => 5, 'cols' => 60, 'disabled' => $configDisabled)) ?>
			<?php echo $formEx->error('Theme.description') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Theme.author', '制作者') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Theme.author', array('type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $configDisabled)) ?>
			<?php echo $formEx->error('Theme.author') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Theme.url', 'URL') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('Theme.url', array('type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $configDisabled)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpUrl', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('Theme.url') ?>
			<div id="helptextUrl" class="helptext">
				<ul>
					<li>制作者のWEBサイトのURL。</li>
					<li>半角のみで入力してください。</li>
				</ul>
			</div>
		</td>
	</tr>
</table>

<?php if(!$folderDisabled): ?>
<div class="align-center">
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削　除', 
			array('action' => 'del', $formEx->value('Theme.name')),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('Theme.name')),
			false); ?>
</div>
<?php endif ?>

<?php echo $formEx->end() ?>