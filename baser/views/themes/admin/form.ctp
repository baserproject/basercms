<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマ フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<?php if($folderDisabled): ?>
<p><span class="required">テーマフォルダに書込権限がありません。</span></p>
<?php endif ?>
<?php if($configDisabled): ?>
<p><span class="required">テーマ設定ファイルに書込権限がありません。</span></p>
<?php endif ?>


<?php echo $formEx->create('Theme', array('action' => 'edit', 'url' => array('action' => 'edit', $theme))) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th class="col-head"><?php echo $formEx->label('Theme.name', 'テーマ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('Theme.name', array('type' => 'text', 'size' => 20, 'maxlength' => 255, 'disabled' => $folderDisabled)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('Theme.name') ?>
				<div id="helptextName" class="helptext"> 半角英数字のみで入力してください。 </div>
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
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('Theme.url') ?>
				<div id="helptextUrl" class="helptext">
					<ul>
						<li>制作者のWEBサイトのURL。</li>
						<li>半角英数字のみで入力してください。</li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php if(!$folderDisabled): ?>
<div class="submit">
	<?php echo $formEx->submit('更新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除', 
			array('action' => 'del', $formEx->value('Theme.name')),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('Theme.name')),
			false); ?>
</div>
<?php endif ?>

<?php echo $formEx->end() ?>