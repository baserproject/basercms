<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログコンテンツ フォーム
 *
 * @var BcAppView $this
 */
$this->BcBaser->i18nScript([
	'confirmMessage1' => __d('baser', 'ブログ設定を保存して、コンテンツテンプレート %s の編集画面に移動します。よろしいですか？')
]);
$this->BcBaser->js('Blog.admin/blog_contents/edit', false);
?>


<h2><?php echo __d('baser', '基本項目') ?></h2>


<?php echo $this->BcForm->create('BlogContent') ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('BlogContent.id', ['type' => 'hidden']) ?>

<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.description', __d('baser', 'ブログ説明文')) ?></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->ckeditor('BlogContent.description', [
					'editorWidth' => 'auto',
					'editorHeight' => '120px',
					'editorToolType' => 'simple',
					'editorEnterBr' => @$siteConfig['editor_enter_br']
				])
				?>
				<?php echo $this->BcForm->error('BlogContent.description') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>
<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption"><?php echo __d('baser', 'オプション') ?></a></h2>
<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="formOptionBody">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.list_count', __d('baser', '一覧表示件数')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogContent.list_count', ['type' => 'text', 'size' => 20, 'maxlength' => 255]) ?>
				&nbsp;<?php echo __d('baser', '件') ?>&nbsp;
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpListCount', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('BlogContent.list_count') ?>
				<div id="helptextListCount" class="helptext">
					<ul>
						<li><?php echo __d('baser', '公開サイトの一覧に表示する件数を指定します。') ?></li>
						<li><?php echo __d('baser', '半角数字で入力してください。') ?></li>
						<li><?php echo __d('baser', '100以下の数値で入力してください。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.list_direction', __d('baser', '一覧に表示する順番')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogContent.list_direction', ['type' => 'select', 'options' => ['DESC' => __d('baser', '新しい記事順'), 'ASC' => __d('baser', '古い記事順')]]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpListDirection', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('BlogContent.list_direction') ?>
				<div id="helptextListDirection" class="helptext">
					<ul>
						<li><?php echo __d('baser', '公開サイトの一覧における記事の並び方向を指定します。') ?></li>
						<li><?php echo __d('baser', '新しい・古いの判断は投稿日が基準となります。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.list_count', __d('baser', 'RSSフィード出力件数')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogContent.feed_count', ['type' => 'text', 'size' => 20, 'maxlength' => 255]) ?>
				&nbsp;件&nbsp;
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpFeedCount', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('BlogContent.feed_count') ?>
				<div id="helptextFeedCount" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'RSSフィードに出力する件数を指定します。') ?></li>
						<li><?php echo __d('baser', '半角数字で入力してください。') ?></li>
						<?php if ($this->action == 'admin_edit'): ?>
							<li><?php echo __d('baser', 'RSSフィードのURLは') ?>
								<?php $this->BcBaser->link(Router::url('/' . $this->BcForm->value('Content.name') . '/index.rss', true), '/' . $this->BcForm->value('Content.name') . '/index.rss', ['target' => '_blank']) ?>
								<?php echo __d('baser', 'となります。') ?></li>
						<?php endif ?>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.comment_use', __d('baser', 'コメント受付機能')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogContent.comment_use', ['type' => 'checkbox', 'label' => __d('baser', '利用する')]) ?>
				<?php echo $this->BcForm->error('BlogContent.comment_use') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.comment_approve', __d('baser', 'コメント承認機能')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogContent.comment_approve', ['type' => 'checkbox', 'label' => __d('baser', '利用する')]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpCommentApprove', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('BlogContent.comment_approve') ?>
				<div id="helptextCommentApprove"
					 class="helptext"><?php echo __d('baser', '承認機能を利用すると、コメントが投稿されてもすぐに公開されず、管理者側で確認する事ができます。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.auth_capthca', __d('baser', 'コメントイメージ認証')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogContent.auth_captcha', ['type' => 'checkbox', 'label' => __d('baser', '利用する')]) ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpAuthCaptcha', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('BlogContent.auth_captcha') ?>
				<div id="helptextAuthCaptcha" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'ブログコメント送信の際、表示された画像の文字入力させる事で認証を行ないます。') ?></li>
						<li><?php echo __d('baser', 'スパムなどいたずら送信が多いが多い場合に設定すると便利です。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.tag_use', __d('baser', 'タグ機能')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogContent.tag_use', ['type' => 'checkbox', 'label' => __d('baser', '利用する')]) ?>
				<?php echo $this->BcForm->error('BlogContent.tag_use') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.widget_area', __d('baser', 'ウィジェットエリア')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->input('BlogContent.widget_area', [
					'type' => 'select',
					'options' => $this->BcForm->getControlsource('WidgetArea.id'),
					'empty' => __d('baser', 'サイト基本設定に従う')])
				?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpWidgetArea', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('BlogContent.widget_area') ?>
				<div id="helptextWidgetArea" class="helptext">
					<?php echo sprintf(__d('baser', 'ブログコンテンツで利用するウィジェットエリアを指定します。<br>ウィジェットエリアは「%s」より追加できます。'), $this->BcBaser->getLink(__d('baser', 'ウィジェットエリア管理'), ['plugin' => null, 'controller' => 'widget_areas', 'action' => 'index'])) ?>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.template', __d('baser', 'コンテンツテンプレート名')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->input('BlogContent.template', [
					'type' => 'select',
					'options' => $this->Blog->getBlogTemplates($this->BcForm->value('Content.site_id'))])
				?>
				<?php echo $this->BcForm->input('BlogContent.edit_blog_template', ['type' => 'hidden']) ?>
				<?php if ($this->action == 'admin_edit'): ?>
					&nbsp;<?php $this->BcBaser->link(__d('baser', '編集する'), 'javascript:void(0)', ['id' => 'EditBlogTemplate', 'class' => 'button-small']) ?>&nbsp;
				<?php endif ?>
				<?php echo $this->Html->image('admin/icn_help.png', ['id' => 'helpTemplate', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('BlogContent.template') ?>
				<div id="helptextTemplate" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'ブログの本体のテンプレートを指定します。') ?></li>
						<li><?php echo __d('baser', '「編集する」からテンプレートの内容を編集する事ができます。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.eye_catch_size_width', __d('baser', 'アイキャッチ画像サイズ')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<span><?php echo __d('baser', 'PCサイズ') ?></span>　
				<small>[<?php echo __d('baser', '幅') ?>
					]</small><?php echo $this->BcForm->input('BlogContent.eye_catch_size_thumb_width', ['type' => 'text', 'size' => '8']) ?>
				&nbsp;px　×　
				<small>[<?php echo __d('baser', '高さ') ?>
					]</small><?php echo $this->BcForm->input('BlogContent.eye_catch_size_thumb_height', ['type' => 'text', 'size' => '8']) ?>
				<br/>
				<span><?php echo __d('baser', '携帯サイズ') ?></span>　
				<small>[<?php echo __d('baser', '幅') ?>
					]</small><?php echo $this->BcForm->input('BlogContent.eye_catch_size_mobile_thumb_width', ['type' => 'text', 'size' => '8']) ?>
				&nbsp;px　×　
				<small>[<?php echo __d('baser', '高さ') ?>
					]</small><?php echo $this->BcForm->input('BlogContent.eye_catch_size_mobile_thumb_height', ['type' => 'text', 'size' => '8']) ?>
				<?php echo $this->BcForm->error('BlogContent.eye_catch_size') ?>
				<div id="helptextTemplate" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'アイキャッチ画像のサイズを指定します。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogContent.use_content', __d('baser', '記事概要')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogContent.use_content', ['type' => 'checkbox', 'label' => __d('baser', '利用する')]) ?>
				<?php echo $this->BcForm->error('BlogContent.use_content') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button']) ?>
</div>

<?php echo $this->BcForm->end() ?>
