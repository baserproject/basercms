<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログカテゴリ フォーム
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
 * @package			baser.plugins.blog.views
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
	<p>ブログ記事をグルーピングする為のカテゴリ登録を行います。<br />
	ブログカテゴリータイトルはTitleタグとして利用されますので、カテゴリを特定するキーワードを登録しましょう。検索エンジン対策として有用です。<br />
	また、各カテゴリは親カテゴリを指定する事ができ、細かく分類分けが可能です。</p>
	<div class="example-box">
		<div class="head">（例）カテゴリ「ニュースリリース」に属する、ブログ記事「新商品を発表しました」のタイトル</div>
		<p>「新商品を発表しました｜ニュースリリース｜サイトタイトル」</p>
	</div>
</div>


<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action=='admin_add'): ?>
    <?php echo $formEx->create('BlogCategory',array('url'=>'/admin/blog/blog_categories/add/'.$blogContent['BlogContent']['id'])) ?>
<?php elseif($this->action=='admin_edit'): ?>
    <?php echo $formEx->create('BlogCategory',array('url'=>'/admin/blog/blog_categories/edit/'.$blogContent['BlogContent']['id'].'/'.$formEx->value('BlogCategory.id'))) ?>
<?php endif; ?>
<?php echo $formEx->hidden('BlogCategory.id') ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogCategory.no', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('BlogCategory.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogCategory.name', 'ブログカテゴリ名') ?></th>
		<td class="col-input">
            <?php echo $formEx->text('BlogCategory.name', array('size'=>40,'maxlength'=>255)) ?>
			<?php echo $html->image('help.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('BlogCategory.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
                    <li>URLに利用されます</li>
					<li>半角のみで入力して下さい</li>
				</ul>
			</div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogCategory.title', 'ブログカテゴリタイトル') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogCategory.title', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('BlogCategory.title') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogCategory.parent_id', '親カテゴリ') ?></th>
		<td class="col-input"><?php echo $formEx->select('BlogCategory.parent_id', $formEx->getControlSource('BlogCategory.parent_id',array('blogContentId'=>$blogContent['BlogContent']['id'],'excludeParentId'=>$formEx->value('BlogCategory.id'))),null,array('escape'=>false),'なし') ?><?php echo $formEx->error('BlogCategory.parent_id') ?>&nbsp;</td>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $blogContent['BlogContent']['id'], $formEx->value('BlogCategory.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('BlogCategory.name')),false); ?>
<?php endif ?>
</div>
