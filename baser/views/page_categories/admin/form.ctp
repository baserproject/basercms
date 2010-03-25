<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ページカテゴリー フォーム
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
<h2><?php $baser->contentsTitle() ?>&nbsp;<?php echo $html->image('help.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
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


<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('PageCategory') ?>
<?php echo $formEx->hidden('PageCategory.id') ?>
<?php echo $formEx->hidden('PageCategory.no') ?>
<?php echo $formEx->hidden('PageCategory.theme') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('PageCategory.no', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('PageCategory.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('PageCategory.name', 'ページカテゴリー名') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('PageCategory.name', array('size'=>40,'maxlength'=>255)) ?>
			<?php echo $html->image('help.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('PageCategory.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
                    <li>ページカテゴリ名はURLで利用します</li>
					<li>半角のみで入力して下さい</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('PageCategory.title', 'ページカテゴリータイトル') ?></th>
		<td class="col-input">
            <?php echo $formEx->text('PageCategory.title', array('size'=>40,'maxlength'=>255)) ?>
			<?php echo $html->image('help.png',array('id'=>'helpTitle','class'=>'help','alt'=>'ヘルプ')) ?>
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
            <?php echo $formEx->select('PageCategory.parent_id', $formEx->getControlSource('parent_id',array('excludeParentId'=>$formEx->value('PageCategory.id'))),null,array('escape'=>false),'なし') ?>
            <?php echo $html->image('help.png',array('id'=>'helpParentId','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('PageCategory.parent_id') ?>
			<div id="helptextParentId" class="helptext">
				<ul>
                    <li>カテゴリの下の階層にカテゴリを作成するには親カテゴリを選択します。</li>
				</ul>
			</div>
        </td>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $formEx->value('PageCategory.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('PageCategory.name')),false); ?>
<?php endif ?>
</div>
