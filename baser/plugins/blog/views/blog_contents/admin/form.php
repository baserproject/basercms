<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログコンテンツ フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
$(window).load(function() {
	$("#BlogContentName").focus();
});
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
	<?php if($bcForm->value('BlogContent.status')): ?>
	<strong>このブログのURL：<?php $bcBaser->link($bcBaser->getUri('/'.$blogContent['BlogContent']['name'].'/index'),'/'.$blogContent['BlogContent']['name'].'/index') ?></strong>
	<?php else: ?>
	<strong>このブログのURL：<?php echo $bcBaser->getUri('/'.$blogContent['BlogContent']['name'].'/index') ?></strong>
	<?php endif ?>
</div>
<?php endif ?>

<!-- form -->
<h2>基本項目</h2>


<?php echo $bcForm->create('BlogContent') ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
	<?php if($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $bcForm->value('BlogContent.id') ?>
				<?php echo $bcForm->input('BlogContent.id', array('type' => 'hidden')) ?>
			</td>
		</tr>
	<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.name', 'ブログアカウント名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.name', array('type' => 'text', 'size'=>40,'maxlength'=>255)) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpCategoryFilter', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.name') ?>
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
			<th class="col-head"><?php echo $bcForm->label('BlogContent.title', 'ブログタイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.title', array('type' => 'text', 'size' => 40, 'maxlength' => 255, 'counter' => true)) ?>
				<?php echo $bcForm->error('BlogContent.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.description', 'ブログ説明文') ?></th>
			<td class="col-input">
				<?php echo $bcForm->ckeditor('BlogContent.description', null, array(
					'width'		=> 'auto', 
					'height'	=> '120px', 
					'type'		=> 'simple',
					'enterBr'	=> @$siteConfig['editor_enter_br']
				)) ?>
				<?php echo $bcForm->error('BlogContent.description') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.exclude_search', '公開設定') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.status', array(
						'type'		=> 'radio',
						'options'	=> array(0 => '非公開', 1 => '公開') ,
						'legend'	=> false,
						'separator'	=> '&nbsp;&nbsp;')) ?><br />
				<?php echo $bcForm->error('BlogContent.status') ?>
				<?php echo $bcForm->input('BlogContent.exclude_search', array('type' => 'checkbox', 'label' => 'このブログのトップページをサイト内検索の検索結果より除外する')) ?>
			</td>
		</tr>
	</table>
</div>
<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption">オプション</a></h2>
<div class="section">
<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="formOptionBody">
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.list_count', '一覧表示件数') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.list_count', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>&nbsp;件&nbsp;
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpListCount', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.list_count') ?>
				<div id="helptextListCount" class="helptext">
					<ul>
						<li>公開サイトの一覧に表示する件数を指定します。</li>
						<li>半角数字で入力してください。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.list_direction', '一覧に表示する順番') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.list_direction', array('type' => 'select', 'options' => array('DESC' => '新しい記事順', 'ASC'=>'古い記事順'))) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpListDirection', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.list_direction') ?>
				<div id="helptextListDirection" class="helptext">
					<ul>
						<li>公開サイトの一覧における記事の並び方向を指定します。</li>
						<li>新しい・古いの判断は投稿日が基準となります。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.list_count', 'RSSフィード出力件数') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.feed_count', array('type' => 'text', 'size' => 20, 'maxlength' => 255)) ?>&nbsp;件&nbsp;
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpFeedCount', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.feed_count') ?>
				<div id="helptextFeedCount" class="helptext">
					<ul>
						<li>RSSフィードに出力する件数を指定します。</li>
						<li>半角数字で入力してください。</li>
						<?php if($this->action == 'admin_edit'): ?>
						<li>RSSフィードのURLは
							<?php $bcBaser->link(Router::url('/'.$bcForm->value('BlogContent.name').'/index.rss', true),'/'.$bcForm->value('BlogContent.name').'/index.rss',array('target'=>'_blank')) ?>
							となります。</li>
						<?php endif ?>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.comment_use', 'コメント受付機能') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.comment_use', array(
					'type'		=> 'radio',
					'options'	=> $bcText->booleanDoList('利用'),
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $bcForm->error('BlogContent.comment_use') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.comment_approve', 'コメント承認機能') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.comment_approve', array(
						'type'		=> 'radio',
						'options'	=> $bcText->booleanDoList('利用'),
						'legend'	=> false,
						'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpCommentApprove', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.comment_approve') ?>
				<div id="helptextCommentApprove" class="helptext">承認機能を利用すると、コメントが投稿されてもすぐに公開されず、管理者側で確認する事ができます。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('MailContent.auth_capthca', 'コメントイメージ認証') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.auth_captcha', array(
						'type'		=> 'radio',
						'options'	=> $bcText->booleanDoList('利用'),
						'legend'	=> false,
						'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpAuthCaptcha', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.auth_captcha') ?>
				<div id="helptextAuthCaptcha" class="helptext">
					<ul>
						<li>ブログコメント送信の際、表示された画像の文字入力させる事で認証を行ないます。</li>
						<li>スパムなどいたずら送信が多いが多い場合に設定すると便利です。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.tag_use', 'タグ機能') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.tag_use', array(
					'type'		=> 'radio',
					'options'	=> $bcText->booleanDoList('利用'),
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $bcForm->error('BlogContent.tag_use') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.widget_area', 'ウィジェットエリア') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.widget_area', array(
						'type'		=> 'select',
						'options'	=> $bcForm->getControlsource('WidgetArea.id'),
						'empty'		=> 'サイト基本設定に従う')) ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpWidgetArea', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.widget_area') ?>
				<div id="helptextWidgetArea" class="helptext">
					ブログコンテンツで利用するウィジェットエリアを指定します。<br />
					ウィジェットエリアは「<?php $bcBaser->link('ウィジェットエリア管理', array('plugin' => null, 'controller' => 'widget_areas', 'action' => 'index')) ?>」より追加できます。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.layout', 'レイアウトテンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.layout', array(
							'type'		=> 'select',
							'options'	=> $blog->getLayoutTemplates())) ?>
				<?php echo $bcForm->input('BlogContent.edit_layout_template', array('type' => 'hidden')) ?>
	<?php if($this->action == 'admin_edit'): ?>
				<?php $bcBaser->link('≫ 編集する', 'javascript:void(0)', array('id' => 'EditLayoutTemplate')) ?>
	<?php endif ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpLayout', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.layout') ?>
				<div id="helptextLayout" class="helptext">
					<ul>
						<li>ブログの外枠のテンプレートを指定します。</li>
						<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.template', 'コンテンツテンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.template', array(
						'type'		=> 'select',
						'options'	=> $blog->getBlogTemplates())) ?>
				<?php echo $bcForm->input('BlogContent.edit_blog_template', array('type' => 'hidden')) ?>
	<?php if($this->action == 'admin_edit'): ?>
				<?php $bcBaser->link('≫ 編集する', 'javascript:void(0)', array('id' => 'EditBlogTemplate')) ?>
	<?php endif ?>
				<?php echo $html->image('admin/icn_help.png', array('id' => 'helpTemplate', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $bcForm->error('BlogContent.template') ?>
				<div id="helptextTemplate" class="helptext">
					<ul>
						<li>ブログの本体のテンプレートを指定します。</li>
						<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
					</ul>
				</div>
			</td>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.eye_catch_size_width', 'アイキャッチ画像サイズ') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<span>PCサイズ</span>　
				<small>[幅]</small><?php echo $bcForm->input('BlogContent.eye_catch_size_thumb_width', array('type' => 'text', 'size' => '8')) ?>&nbsp;px　×　
				<small>[高さ]</small><?php echo $bcForm->input('BlogContent.eye_catch_size_thumb_height', array('type' => 'text', 'size' => '8')) ?><br />
				<span>携帯サイズ</span>　
				<small>[幅]</small><?php echo $bcForm->input('BlogContent.eye_catch_size_mobile_thumb_width', array('type' => 'text', 'size' => '8')) ?>&nbsp;px　×　
				<small>[高さ]</small><?php echo $bcForm->input('BlogContent.eye_catch_size_mobile_thumb_height', array('type' => 'text', 'size' => '8')) ?>
				<?php echo $bcForm->error('BlogContent.eye_catch_size') ?>
				<div id="helptextTemplate" class="helptext">
					<ul>
						<li>アイキャッチ画像のサイズを指定します。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $bcForm->label('BlogContent.use_content', '記事概要') ?></th>
			<td class="col-input">
				<?php echo $bcForm->input('BlogContent.use_content', array(
					'type'		=> 'radio',
					'options'	=> $bcText->booleanDoList('利用'),
					'legend'	=> false,
					'separator'	=> '&nbsp;&nbsp;')) ?>
				<?php echo $bcForm->error('BlogContent.tag_use') ?>
			</td>
		</tr>
	</table>
</div>
<!-- button -->
<div class="submit">
	<?php echo $bcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
<?php if($this->action == 'admin_edit'): ?>
	<?php $bcBaser->link('削除', 
			array('action' => 'delete', $bcForm->value('BlogContent.id')),
			array('class' => 'button'),
			sprintf('%s を本当に削除してもいいですか？', $bcForm->value('BlogContent.title')),
			false); ?>
<?php endif ?>
</div>

<?php echo $bcForm->end() ?>