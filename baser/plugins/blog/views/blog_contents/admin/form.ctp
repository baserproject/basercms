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
<h2><?php $baser->contentsTitle() ?>&nbsp;<?php echo $html->image('help.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ブログコンテンツの基本設定を登録します。<br />
	各項目のヘルプメッセージを確認し登録を完了させてください。<br />
	ブログごとにデザインを変更する事もできます。その場合、画面下の「オプション」をクリックし、テンプレート名を変更します。<br />
	<small>※ テンプレート名を変更した場合は、新しい名称のテンプレートを作成しアップロードする必要があります。</small><br />
	<a href="http://basercms.net/manuals/designers/5.html" target="_blank">≫ ブログのテンプレートを変更する</a></p>
</div>


<h3>基本項目</h3>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('BlogContent') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogContent.id', 'ID') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('BlogContent.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.name', 'ブログアカウント名') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.name', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $formEx->error('BlogContent.name') ?>
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
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.title', 'ブログタイトル') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.title', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('BlogContent.title') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogContent.description', 'ブログ説明文') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('BlogContent.description', array('cols'=>35,'rows'=>4)) ?><?php echo $formEx->error('BlogContent.description') ?>&nbsp;</td>
	</tr>
</table>


<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>


<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogContent.comment_use', 'コメント受付機能') ?></th>
		<td class="col-input">
            <?php echo $formEx->radio('BlogContent.comment_use', $textEx->booleanDoList('利用'),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
            <?php echo $formEx->error('BlogContent.comment_use') ?>
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogContent.comment_approve', 'コメント承認機能') ?></th>
		<td class="col-input">
            <?php echo $formEx->radio('BlogContent.comment_approve', $textEx->booleanDoList('利用'),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
            <?php echo $html->image('help.png',array('id'=>'helpCommentApprove','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('BlogContent.comment_approve') ?>
            <div id="helptextCommentApprove" class="helptext">
                承認機能を利用すると、コメントが投稿されてもすぐに公開されず、管理者側で確認する事ができます。
            </div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.layout', 'レイアウトテンプレート名') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.layout', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $formEx->error('BlogContent.layout') ?>
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
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.template', 'コンテンツテンプレート名') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.template', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $formEx->error('BlogContent.template') ?>
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
<th class="col-head"><?php echo $formEx->label('BlogContent.admin_viewcnt', 'テーマ') ?></th>
	<td class="col-input">
    	<?php echo $formEx->select('BlogContent.theme', $themes,null,null,'基本設定に従う') ?>
        &nbsp;
    </td>
</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $formEx->value('BlogContent.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('BlogContent.title')),false); ?>
<?php endif ?>
</div>
