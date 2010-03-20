<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログコンテンツ フォーム
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
<?php if($this->action == 'admin_view'): ?>
<?php $freeze->freeze(); ?>
<?php endif; ?>

<h3>基本項目</h3>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $form->create('BlogContent') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_view' || $this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $form->label('BlogContent.id', 'ID') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('BlogContent.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('BlogContent.name', 'ブログアカウント名') ?></th>
		<td class="col-input"><?php echo $freeze->text('BlogContent.name', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $form->error('BlogContent.name') ?>
            <?php echo $html->image('help.png',array('id'=>'helpCategoryFilter','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextCategoryFilter" class="helptext">
                <ul>
                    <li>ブログのURLに利用します。<br />(例)ブログIDが test の場合・・・http://example/test/</li>
                    <li>半角英数字で入力して下さい。</li>
                </ul>
            </div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('BlogContent.title', 'ブログタイトル') ?></th>
		<td class="col-input"><?php echo $freeze->text('BlogContent.title', array('size'=>40,'maxlength'=>255)) ?><?php echo $form->error('BlogContent.title') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('BlogContent.description', 'ブログ説明文') ?></th>
		<td class="col-input"><?php echo $freeze->textarea('BlogContent.description', array('cols'=>35,'rows'=>4)) ?><?php echo $form->error('BlogContent.description') ?>&nbsp;</td>
	</tr>
</table>


<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>


<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><?php echo $form->label('BlogContent.comment_use', 'コメント受付機能') ?></th>
		<td class="col-input">
            <?php echo $freeze->radio('BlogContent.comment_use', $textEx->booleanDoList('利用'),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
            <?php echo $form->error('BlogContent.comment_use') ?>
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('BlogContent.comment_approve', 'コメント承認機能') ?></th>
		<td class="col-input">
            <?php echo $freeze->radio('BlogContent.comment_approve', $textEx->booleanDoList('利用'),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
            <?php echo $html->image('help.png',array('id'=>'helpCommentApprove','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $form->error('BlogContent.comment_approve') ?>
            <div id="helptextCommentApprove" class="helptext">
                承認機能を利用すると、コメントが投稿されてもすぐに公開されず、管理者側で確認する事ができます。
            </div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('BlogContent.layout', 'レイアウトテンプレート名') ?></th>
		<td class="col-input"><?php echo $freeze->text('BlogContent.layout', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $form->error('BlogContent.layout') ?>
            &nbsp;
            <?php echo $html->image('help.png',array('id'=>'helpLayout','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextLayout" class="helptext">
                <ul>
                    <li>ブログの外枠のテンプレート名を指定します。初期値：default</li>
                    <li>半角英数字で入力して下さい。</li>
                </ul>
            </div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('BlogContent.template', 'コンテンツテンプレート名') ?></th>
		<td class="col-input"><?php echo $freeze->text('BlogContent.template', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $form->error('BlogContent.template') ?>
            <?php echo $html->image('help.png',array('id'=>'helpTemplate','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextTemplate" class="helptext">
                <ul>
                    <li>ブログの内枠のテンプレート名を指定します。初期値：default</li>
                    <li>半角英数字で入力して下さい。</li>
                </ul>
            </div>
            &nbsp;
        </td>
	</tr>
<tr>
<th class="col-head"><?php echo $form->label('BlogContent.admin_viewcnt', 'テーマ') ?></th>
	<td class="col-input">
    	<?php echo $form->select('BlogContent.theme', $themes,null,null,'基本設定に従う') ?>
        &nbsp;
    </td>
</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $form->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $form->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
<?php else: ?>
	<?php $baser->link('編集する',array('action'=>'edit',$form->value('BlogContent.id')),array('class'=>'btn-orange button'),null,false) ?>　
	<?php $baser->link('削除する',array('action'=>'delete', $form->value('BlogContent.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('BlogContent.name')),false); ?>
<?php endif ?>
</div>
