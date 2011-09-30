<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] グローバルメニューフォーム
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
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

<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>公開ページ・管理画面のどちらかのタイプを選択してグローバルメニューのアイテムを登録します。<br />
	<ul>
		<li>一時的に利用しない場合には、利用状態を「利用しない」に設定します。</li>
	</ul>
</div>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('GlobalMenu') ?>
<?php echo $formEx->input('GlobalMenu.id', array('type' => 'hidden')) ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><?php echo $formEx->label('GlobalMenu.menu_type', 'タイプ') ?></th>
		<td class="col-input">
			<?php $menuTypes = $formEx->getControlSource('GlobalMenu.menu_type') ?>
			<?php echo $menuTypes[$formEx->value('GlobalMenu.menu_type')] ?>
			<?php echo $formEx->input('GlobalMenu.menu_type', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('GlobalMenu.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->value('GlobalMenu.no') ?>
			<?php echo $formEx->input('GlobalMenu.no', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('GlobalMenu.name', 'メニュー名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('GlobalMenu.name', array('type' => 'text', 'size' => 40, 'maxlength' => 20)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>日本語が利用できます。</li>
					<li>識別しやすくわかりやすい名前を入力します。</li>
				</ul>
			</div>
			<?php echo $formEx->error('GlobalMenu.name') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('GlobalMenu.link', 'リンクURL') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('GlobalMenu.link', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpLink', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('GlobalMenu.link') ?>
			<div id="helptextLink" class="helptext"> 先頭にスラッシュつけたルートパスで入力してください。<br />
				(例) /admin/global/index </div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('GlobalMenu.status', '利用状態') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('GlobalMenu.status', array(
					'type'		=> 'radio',
					'options'	=> $textEx->booleanDoList("利用"),
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
			<?php echo $formEx->error('GlobalMenu.status') ?>
		</td>
	</tr>
</table>

<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削　除', 
			array('action' => 'delete', $formEx->value('GlobalMenu.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('GlobalMenu.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>