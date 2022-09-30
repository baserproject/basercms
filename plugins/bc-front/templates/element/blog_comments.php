<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ブログコメント一覧
 * 呼出箇所：ブログ記事詳細
 *
 * @var BcAppView $this
 * @var array $blogContent ブログコンテンツデータ
 */
?>

<?php echo $this->element('blog_comments_scripts'); ?>

<?php $captchaId = mt_rand(0, 99999999) ?>
<div id="BlogCommentCaptchaUrl" style="display:none"><?php echo $this->BcBaser->getUrl('/blog/blog_comments/captcha/' . $captchaId) ?></div>
<div id="BlogCommentGetTokenUrl" style="display:none"><?php echo $this->BcBaser->getUrl('/blog/blog_comments/get_token') ?></div>

<?php if (!empty($blogContent['BlogContent']['comment_use'])): ?>
<div class="bs-blog-comment">

	<h4 class="bs-blog-comment__head"><?php echo __('この記事へのコメント') ?></h4>

	<div id="BlogCommentList" class="bs-blog-comment__list">
		<?php if (!empty($post['BlogComment'])): ?>
			<?php foreach ($post['BlogComment'] as $comment): ?>
				<!-- /Elements/blog_comment.php -->
				<?php $this->BcBaser->element('blog_comment', ['dbData' => $comment]) ?>
			<?php endforeach ?>
		<?php else: ?>
		<p>コメントはまだありません。</p>
		<?php endif ?>
	</div>

	<h4 class="bs-blog-comment__head"><?php echo __('コメントを送る') ?></h4>

	<?php echo $this->BcForm->create('BlogComment', [
		'url' => '/blog/blog_comments/add/' . $blogContent['BlogContent']['id'] . '/' . $post['BlogPost']['id'],
		'id' => 'BlogCommentAddForm'
	]) ?>
	<?php echo $this->BcForm->input('BlogComment.captcha_id', [
		'type' => 'hidden',
		'value' => $captchaId
	]) ?>

	<table class="bs-blog-comment__form">
		<tr>
			<th><?php echo $this->BcForm->label('BlogComment.name', __('お名前') . ' / ' . __('ニックネーム')) ?><span class="required">必須</span></th>
			<td><?php echo $this->BcForm->input('BlogComment.name', ['type' => 'text', 'required' => false]) ?></td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('BlogComment.email', __('メールアドレス')) ?><span class="required">必須</span></th>
			<td>
				<?php echo $this->BcForm->input('BlogComment.email', ['type' => 'text', 'size' => 30, 'required' => false]) ?>&nbsp;
				<br><small>※ <?php echo __('メールアドレスは公開されません') ?></small>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('BlogComment.url', 'URL') ?><span class="normal">任意</span></th>
			<td><?php echo $this->BcForm->input('BlogComment.url', ['type' => 'text', 'size' => 30, 'required' => false]) ?></td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('BlogComment.message', __('コメント')) ?><span class="required">必須</span></th>
			<td><?php echo $this->BcForm->input('BlogComment.message', ['type' => 'textarea', 'rows' => 10, 'cols' => 52, 'required' => false]) ?></td>
		</tr>
	</table>

	<?php if ($blogContent['BlogContent']['auth_captcha']): ?>
	<div class="bs-blog-comment__auth-captcha">
		<img src="" alt="<?php echo __('認証画象') ?>" class="auth-captcha-image" id="AuthCaptchaImage" style="display:none">
		<?php $this->BcBaser->img('admin/captcha_loader.gif', [
			'alt' => 'Loading...',
			'class' => 'auth-captcha-image',
			'id' => 'CaptchaLoader'
		]) ?>
		<?php echo $this->BcForm->text('BlogComment.auth_captcha') ?>
		&nbsp;<?php echo __('画像の文字を入力してください') ?>
	</div>
	<?php endif ?>

	<div class="bs-blog-comment__submit">
		<?php echo $this->BcForm->end(['label' => __('送信する'), 'id' => 'BlogCommentAddButton', 'class' => 'button']) ?>
	</div>

	<div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>

</div>
<?php endif ?>
