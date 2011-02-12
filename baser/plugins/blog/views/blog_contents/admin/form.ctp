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
<script type="text/javascript">
$(function(){
	$("#EditLayoutTemplate").click(function(){
		if(confirm('ブログ設定を保存して、レイアウトテンプレート '+$("#BlogContentLayout").val()+' の編集画面に移動します。よろしいですか？')){
			$("#BlogContentEditLayoutTemplate").val(1);
			$("#BlogContentEditBlogTemplate").val('');
			$("#BlogContentEditForm").submit();
		}
	});
	$("#EditBlogTemplate").click(function(){
		if(confirm('ブログ設定を保存して、コンテンツテンプレート '+$("#BlogContentTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#BlogContentEditLayoutTemplate").val('');
			$("#BlogContentEditBlogTemplate").val(1);
			$("#BlogContentEditForm").submit();
		}
	});
});
</script>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ブログコンテンツの基本設定を登録します。<br />
		各項目のヘルプメッセージを確認し登録を完了させてください。<br />
		ブログごとにデザインを変更する事もできます。その場合、画面下の「オプション」をクリックし、テンプレート名を変更します。<br />
		<small>※ テンプレート名を変更した場合は、新しい名称のテンプレートを作成しアップロードする必要があります。</small><br />
		<a href="http://basercms.net/manuals/designers/5.html" target="_blank">≫ ブログのテンプレートを変更する</a></p>
</div>

<?php if($this->action == 'admin_edit'): ?>
<p><strong>このブログのURL：<?php $baser->link($baser->getUri('/'.$blogContent['BlogContent']['name'].'/index'),'/'.$blogContent['BlogContent']['name'].'/index',array('target'=>'_blank')) ?></strong></p>
<?php endif ?>

<h3>基本項目</h3>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $formEx->create('BlogContent') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogContent.id', 'NO') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp; </td>
	</tr>
	<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.name', 'ブログアカウント名') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.name', array('size'=>40,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpCategoryFilter','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextCategoryFilter" class="helptext">
				<ul>
					<li>ブログのURLに利用します。<br />
						(例)ブログIDが test の場合・・・http://example/test/</li>
					<li>半角英数字で入力して下さい。</li>
				</ul>
			</div>
			<?php echo $formEx->error('BlogContent.name') ?></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.title', 'ブログタイトル') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.title', array('size'=>40,'maxlength'=>255)) ?> <?php echo $formEx->error('BlogContent.title') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogContent.description', 'ブログ説明文') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('BlogContent.description', array('cols'=>35,'rows'=>4)) ?> <?php echo $formEx->error('BlogContent.description') ?>&nbsp;</td>
	</tr>
</table>
<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.list_count', '一覧表示件数') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.list_count', array('size'=>20,'maxlength'=>255)) ?>&nbsp;件&nbsp; <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpListCount','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextListCount" class="helptext">
				<ul>
					<li>公開サイトの一覧に表示する件数を指定します。</li>
					<li>半角数字で入力して下さい。</li>
				</ul>
			</div>
			<?php echo $formEx->error('BlogContent.list_count') ?></td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogContent.list_direction', '一覧に表示する順番') ?></th>
		<td class="col-input"><?php echo $formEx->select('BlogContent.list_direction', array('DESC'=>'新しい記事順', 'ASC'=>'古い記事順'),null,null,false) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpListDirection','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextListDirection" class="helptext">
				<ul>
					<li>公開サイトの一覧における記事の並び方向を指定します。</li>
					<li>新しい・古いの判断は投稿日が基準となります。</li>
				</ul>
			</div>
			<?php echo $formEx->error('BlogContent.list_direction') ?></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.list_count', 'RSSフィード出力件数') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogContent.feed_count', array('size'=>20,'maxlength'=>255)) ?>&nbsp;件&nbsp; <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpFeedCount','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextFeedCount" class="helptext">
				<ul>
					<li>RSSフィードに出力する件数を指定します。</li>
					<li>半角数字で入力して下さい。</li>
					<li>RSSフィードのURLは
						<?php $baser->link($baser->getUrl('http://'.env('HTTP_HOST').'/'.$formEx->value('BlogContent.name').'/index.rss'),'/'.$formEx->value('BlogContent.name').'/index.rss',array('target'=>'_blank')) ?>
						となります。</li>
				</ul>
			</div>
			<?php echo $formEx->error('BlogContent.feed_count') ?></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.comment_use', 'コメント受付機能') ?></th>
		<td class="col-input"><?php echo $formEx->radio('BlogContent.comment_use', $textEx->booleanDoList('利用'),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?> <?php echo $formEx->error('BlogContent.comment_use') ?></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.comment_approve', 'コメント承認機能') ?></th>
		<td class="col-input"><?php echo $formEx->radio('BlogContent.comment_approve', $textEx->booleanDoList('利用'),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpCommentApprove','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextCommentApprove" class="helptext"> 承認機能を利用すると、コメントが投稿されてもすぐに公開されず、管理者側で確認する事ができます。 </div>
			<?php echo $formEx->error('BlogContent.comment_approve') ?></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.auth_capthca', 'コメントイメージ認証') ?></th>
		<td class="col-input">
			<?php echo $formEx->radio('BlogContent.auth_captcha',$textEx->booleanDoList('利用'),array('legend'=>false)) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAuthCaptcha','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('BlogContent.auth_captcha') ?>
			<div id="helptextAuthCaptcha" class="helptext">
				<ul>
					<li>ブログコメント送信の際、表示された画像の文字入力させる事で認証を行ないます。</li>
					<li>スパムなどいたずら送信が多いが多い場合に設定すると便利です。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.widget_area', 'ウィジェットエリア') ?></th>
		<td class="col-input">
			<?php echo $formEx->select('BlogContent.widget_area',$formEx->getControlsource('WidgetArea.id'),null,array(),'サイト基本設定に従う') ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpWidgetArea','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextWidgetArea" class="helptext">
				ブログコンテンツで利用するウィジェットエリアを指定します。<br />
				ウィジェットエリアは「<?php $baser->link('ウィジェットエリア管理',array('plugin'=>null,'controller'=>'widget_areas','action'=>'index')) ?>」より追加できます。
			</div>
			<?php echo $formEx->error('BlogContent.widget_area') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.layout', 'レイアウトテンプレート名') ?></th>
		<td class="col-input">
			<?php echo $formEx->select('BlogContent.layout',$blog->getLayoutTemplates(),null,array(),false) ?>
			<?php echo $formEx->hidden('BlogContent.edit_layout_template') ?>
			<?php if($this->action == 'admin_edit'): ?>
			<?php $baser->link('≫ 編集する','javascript:void(0)',array('id'=>'EditLayoutTemplate')) ?>
			<?php endif ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpLayout','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextLayout" class="helptext">
				<ul>
					<li>ブログの外枠のテンプレートを指定します。</li>
					<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
				</ul>
			</div>
			<?php echo $formEx->error('BlogContent.layout') ?></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogContent.template', 'コンテンツテンプレート名') ?></th>
		<td class="col-input">
			<?php echo $formEx->select('BlogContent.template',$blog->getBlogTemplates(),null,array(),false) ?>
			<?php echo $formEx->hidden('BlogContent.edit_blog_template') ?>
			<?php if($this->action == 'admin_edit'): ?>
			<?php $baser->link('≫ 編集する','javascript:void(0)',array('id'=>'EditBlogTemplate')) ?>
			<?php endif ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpTemplate','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextTemplate" class="helptext">
				<ul>
					<li>ブログの本体のテンプレートを指定します。</li>
					<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
				</ul>
			</div>
			<?php echo $formEx->error('BlogContent.template') ?></td>
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
