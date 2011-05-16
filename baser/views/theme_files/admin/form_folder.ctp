<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマフォルダ登録・編集
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

<h2><?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>テーマファイルを分類する為のフォルダの作成・編集・削除が行えます。</p>
	<ul>
		<li>フォルダを作成するには、フォルダ名を半角で入力して「作成」ボタンをクリックします。</li>
		<li>フォルダ名を編集するには、新しいフォルダ名を半角で入力して「更新」ボタンをクリックします。</li>
		<li>フォルダを削除するには、「削除」ボタンをクリックします。フォルダ内のファイルは全て削除されるので注意が必要です。</li>
		<li>フォルダごと現在のテーマにコピーするには、「現在のテーマにコピー」ボタンをクリックします。（core テーマのみ）</li>
	</ul>
</div>

<!-- current -->
<p><strong>現在の位置：<?php echo $currentPath ?></strong></p>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php if($this->action == 'admin_add_folder'): ?>
<?php echo $formEx->create('ThemeFolder', array('id' => 'TemplateForm', 'url' => array('controller' => 'theme_files', 'action' => 'add_folder', $theme, $type, $path))) ?>
<?php else: ?>
<?php echo $formEx->create('ThemeFolder',array('id' => 'TemplateForm', 'url' => array('controller' => 'theme_files', 'action' => 'edit_folder', $theme, $type, $path))) ?>
<?php endif ?>

<?php echo $formEx->input('ThemeFolder.parent', array('type' => 'hidden')) ?>
<?php echo $formEx->input('ThemeFolder.pastname', array('type' => 'hidden')) ?>

<!-- form -->
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('ThemeFolder.name', 'フォルダ名') ?></th>
		<td class="col-input">
<?php if($this->action != 'admin_view_folder'): ?>
			<?php echo $formEx->input('ThemeFolder.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>フォルダ名は半角で入力してください。</li>
				</ul>
			</div>
			<?php echo $formEx->error('ThemeFolder.name') ?>
<?php else: ?>
			<?php echo $formEx->input('ThemeFolder.name', array('type' => 'text', 'size' => 40, 'readonly' => 'readonly')) ?>
<?php endif ?>
		</td>
	</tr>
</table>

<div class="submit">
<?php if($this->action == 'admin_add_folder'): ?>
	<?php $baser->link('一覧に戻る', array('action' => 'index', $theme, $plugin, $type, $path), array('class' => 'btn-gray button')); ?>
<?php else: ?>
	<?php $baser->link('一覧に戻る', array('action' => 'index', $theme, $plugin, $type, dirname($path)), array('class' => 'btn-gray button')); ?>
<?php endif ?>
<?php if($this->action == 'admin_add_folder'): ?>
	<?php echo $formEx->submit('作　成', array('div' => false, 'class' => 'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit_folder'): ?>
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削　除',
			array('action'=>'del', $theme, $type, $path),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('ThemeFolder.name')),
			false
	) ?>
<?php else: ?>
	<?php if(!$safeModeOn): ?>
		<?php if($theme == 'core'): ?>
	<?php $baser->link('現在のテーマにコピー',
			array('action' => 'copy_folder_to_theme', $theme, $plugin, $type , $path),
			array('class' => 'btn-red button'),
			'本当に現在のテーマ「'.Inflector::camelize($siteConfig['theme']).'」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。'
	) ?>
		<?php endif ?>
	<?php else: ?>
	機能制限のセーフモードで動作していますので、現在のテーマへのコピーはできません。
	<?php endif ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>