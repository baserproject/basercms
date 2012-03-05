<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログコンテンツ フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
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

<?php if($this->action == 'admin_edit'): ?>
<div class="em-box align-left">
	<strong>このブログのURL：<?php $baser->link($baser->getUri('/'.$blogContent['BlogContent']['name'].'/index'),'/'.$blogContent['BlogContent']['name'].'/index') ?></strong>
</div>
<?php endif ?>

<!-- form -->
<h2>基本項目</h2>


<?php echo $formEx->create('BlogContent') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $formEx->value('BlogContent.id') ?>
				<?php echo $formEx->input('BlogContent.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.name', 'ブログアカウント名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.name', array('type' => 'text', 'size'=>40,'maxlength'=>255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpCategoryFilter', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogContent.name') ?>
				<div id="helptextCategoryFilter" class="helptext">
					<ul>
						<li>ブログのURLに利用します。<br />
							(例)ブログアカウント名が test の場合・・・http://example/test/</li>
						<li>半角英数字で入力してください。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.title', 'ブログタイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.title', array('type' => 'text', 'size' => 40, 'maxlength' => 255, 'counter' => true)) ?>
				<?php echo $formEx->error('BlogContent.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.description', 'ブログ説明文') ?></th>
			<td class="col-input">
				<?php echo $formEx->ckeditor('BlogContent.description', null, array('width' => 'autos', 'height' => '120px', 'type' => 'simple')) ?>
				<?php echo $formEx->error('BlogContent.description') ?>
			</td>
		</tr>
	</table>
</div>
<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption">オプション</a></h2>
<div class="section">
<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="formOptionBody">
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.list_count', '一覧表示件数') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.list_count', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>&nbsp;件&nbsp;
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpListCount', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogContent.list_count') ?>
				<div id="helptextListCount" class="helptext">
					<ul>
						<li>公開サイトの一覧に表示する件数を指定します。</li>
						<li>半角数字で入力してください。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.list_direction', '一覧に表示する順番') ?></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.list_direction', array('type' => 'select', 'options' => array('DESC' => '新しい記事順', 'ASC'=>'古い記事順'))) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpListDirection', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogContent.list_direction') ?>
				<div id="helptextListDirection" class="helptext">
					<ul>
						<li>公開サイトの一覧における記事の並び方向を指定します。</li>
						<li>新しい・古いの判断は投稿日が基準となります。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.list_count', 'RSSフィード出力件数') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.feed_count', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>&nbsp;件&nbsp;
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpFeedCount', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogContent.feed_count') ?>
				<div id="helptextFeedCount" class="helptext">
					<ul>
						<li>RSSフィードに出力する件数を指定します。</li>
						<li>半角数字で入力してください。</li>
						<?php if($this->action == 'admin_edit'): ?>
						<li>RSSフィードのURLは
							<?php $baser->link(Router::url('/'.$formEx->value('BlogContent.name').'/index.rss', true),'/'.$formEx->value('BlogContent.name').'/index.rss',array('target'=>'_blank')) ?>
							となります。</li>
						<?php endif ?>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.comment_use', 'コメント受付機能') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.comment_use', array(
					'type'		=> 'radio',
					'options'	=> $textEx->booleanDoList('利用'),
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $formEx->error('BlogContent.comment_use') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.comment_approve', 'コメント承認機能') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.comment_approve', array(
						'type'		=> 'radio',
						'options'	=> $textEx->booleanDoList('利用'),
						'legend'	=> false,
						'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpCommentApprove', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogContent.comment_approve') ?>
				<div id="helptextCommentApprove" class="helptext">承認機能を利用すると、コメントが投稿されてもすぐに公開されず、管理者側で確認する事ができます。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('MailContent.auth_capthca', 'コメントイメージ認証') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.auth_captcha', array(
						'type'		=> 'radio',
						'options'	=> $textEx->booleanDoList('利用'),
						'legend'	=> false,
						'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpAuthCaptcha', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
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
			<th class="col-head"><?php echo $formEx->label('BlogContent.tag_use', 'タグ機能') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.tag_use', array(
					'type'		=> 'radio',
					'options'	=> $textEx->booleanDoList('利用'),
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $formEx->error('BlogContent.tag_use') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.widget_area', 'ウィジェットエリア') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.widget_area', array(
						'type'		=> 'select',
						'options'	=> $formEx->getControlsource('WidgetArea.id'),
						'empty'		=> 'サイト基本設定に従う')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpWidgetArea', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogContent.widget_area') ?>
				<div id="helptextWidgetArea" class="helptext">
					ブログコンテンツで利用するウィジェットエリアを指定します。<br />
					ウィジェットエリアは「<?php $baser->link('ウィジェットエリア管理', array('plugin' => null, 'controller' => 'widget_areas', 'action' => 'index')) ?>」より追加できます。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.layout', 'レイアウトテンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.layout', array(
							'type'		=> 'select',
							'options'	=> $blog->getLayoutTemplates())) ?>
				<?php echo $formEx->input('BlogContent.edit_layout_template', array('type' => 'hidden')) ?>
	<?php if($this->action == 'admin_edit'): ?>
				<?php $baser->link('≫ 編集する', 'javascript:void(0)', array('id' => 'EditLayoutTemplate')) ?>
	<?php endif ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpLayout', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogContent.layout') ?>
				<div id="helptextLayout" class="helptext">
					<ul>
						<li>ブログの外枠のテンプレートを指定します。</li>
						<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.template', 'コンテンツテンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.template', array(
						'type'		=> 'select',
						'options'	=> $blog->getBlogTemplates())) ?>
				<?php echo $formEx->input('BlogContent.edit_blog_template', array('type' => 'hidden')) ?>
	<?php if($this->action == 'admin_edit'): ?>
				<?php $baser->link('≫ 編集する', 'javascript:void(0)', array('id' => 'EditBlogTemplate')) ?>
	<?php endif ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpTemplate', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $formEx->error('BlogContent.template') ?>
				<div id="helptextTemplate" class="helptext">
					<ul>
						<li>ブログの本体のテンプレートを指定します。</li>
						<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $formEx->label('BlogContent.exclude_search', '公開設定') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $formEx->input('BlogContent.exclude_search', array('type' => 'checkbox', 'label' => 'このブログのトップページをサイト内検索の検索結果より除外する')) ?>
			</td>
		</tr>
	</table>
</div>
<!-- button -->
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->submit('登録', array('div' => false, 'class' => 'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->submit('更新', array('div' => false, 'class' => 'btn-orange button')) ?>
	<?php $baser->link('削除', 
			array('action' => 'delete', $formEx->value('BlogContent.id')),
			array('class' => 'btn-gray button'),
			sprintf('%s を本当に削除してもいいですか？', $formEx->value('BlogContent.title')),
			false); ?>
<?php endif ?>
</div>

<?php echo $formEx->end() ?>