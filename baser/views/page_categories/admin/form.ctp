<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリー フォーム
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

<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ページをグルーピングする為のカテゴリ登録を行います。<br />
		ページカテゴリータイトルはTitleタグとして利用されますので、カテゴリを特定するキーワードを登録しましょう。検索エンジン対策として有用です。<br />
		また、各カテゴリは親カテゴリを指定する事ができ、細かく分類分けが可能です。</p>
	<div class="example-box">
		<div class="head">（例）カテゴリ「company」に属する、ページ名「about」として作成したページのタイトル</div>
		<p>※「company」のページカテゴリタイトルを「会社案内」、「about」のタイトルを「コンセプト」として登録</p>
		<p>「コンセプト｜会社案内｜サイトタイトル」</p>
	</div>
</div>

<?php if($this->action == 'admin_edit' && $indexPage): ?>
	<?php if($indexPage['status']): ?>
	<p><strong>このカテゴリのURL：<?php $baser->link($baser->getUri('/' . $indexPage['url']), '/' . $indexPage['url'], array('target' => '_blank')) ?></strong></p>
	<?php else: ?>
	<p><strong>このカテゴリのURL：<?php echo $baser->getUri('/' . $indexPage['url']) ?></strong></p>
	<?php endif ?>
<?php endif ?>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('PageCategory') ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('PageCategory.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->value('PageCategory.id') ?>
			<?php echo $formEx->input('PageCategory.id', array('type' => 'hidden')) ?>
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('PageCategory.name', 'ページカテゴリー名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('PageCategory.name', array('type' => 'text', 'size' => 40, 'maxlength' => 50)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('PageCategory.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>ページカテゴリ名はURLで利用します。</li>
					<li>日本語の入力が可能です。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('PageCategory.title', 'ページカテゴリータイトル') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('PageCategory.title', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpTitle', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('PageCategory.title') ?>
			<div id="helptextTitle" class="helptext">
				<ul>
					<li>ページカテゴリタイトルはTitleタグとして出力されます。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('PageCategory.parent_id', '親カテゴリ') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('PageCategory.parent_id', array(
					'type'		=> 'select', 
					'options'	=> $formEx->getControlSource('parent_id', array('excludeParentId' => $formEx->value('PageCategory.id'))),
					'escape'	=> false,
					'empty'		=> 'なし')) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpParentId', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('PageCategory.parent_id') ?>
			<div id="helptextParentId" class="helptext">
				<ul>
					<li>カテゴリの下の階層にカテゴリを作成するには親カテゴリを選択します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('PageCategory.contents_navi', 'コンテンツナビ') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('PageCategory.contents_navi', array(
					'type'		=> 'radio',
					'options'	=> $textEx->booleanDolist('利用'),
					'legend'	=> false,
					'separator'		=> '&nbsp;&nbsp;')) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpContentsNavi', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('PageCategory.parent_id') ?>
			<div id="helptextContentsNavi" class="helptext">
				<ul>
					<li>同カテゴリ内のページ間ナビゲーション（コンテンツナビ）を利用するには「利用する」を選択します。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('PageCategory.owner_id', '管理グループ') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('PageCategory.owner_id', array(
					'type'		=> 'select',
					'options'	=> $formEx->getControlSource('PageCategory.owner_id'),
					'empty'		=> '指定しない')) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpOwnerId', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('PageCategory.owner_id') ?>
			<div id="helptextOwnerId" class="helptext">
				<ul>
					<li>管理グループを指定した場合、このカテゴリに属したページは、管理グループのユーザーしか編集する事ができなくなります。</li>
				</ul>
			</div>
		</td>
	</tr>
</table>

<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登　録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit' && $formEx->value('PageCategory.name')!='mobile'): ?>
	<?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削　除', 
			array('action'=>'delete', $formEx->value('PageCategory.id')),
			array('class'=>'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？\n\nこのカテゴリに関連するページは、どのカテゴリにも関連しない状態として残ります。', $formEx->value('PageCategory.name')),
			false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>