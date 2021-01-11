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
 * [PUBLISH] ブログコメント一覧
 */
?>

<?php echo $this->element('blog_comments_scripts'); ?>

<?php $captchaId = mt_rand(0, 99999999) ?>
<div id="BlogCommentCaptchaUrl"
	 style="display:none"><?php echo $this->BcBaser->getUrl('/blog/blog_comments/captcha/' . $captchaId) ?></div>
<div id="BlogCommentGetTokenUrl"
	 style="display:none"><?php echo $this->BcBaser->getUrl('/blog/blog_comments/get_token') ?></div>

<?php if ($blogContent['BlogContent']['comment_use']): ?>
	<div id="BlogComment">

		<h4 class="contents-head"><?php echo __('この記事へのコメント') ?></h4>

		<div id="BlogCommentList">
			<?php if (!empty($post['BlogComment'])): ?>
				<?php foreach($post['BlogComment'] as $comment): ?>
					<?php $this->BcBaser->element('blog_comment', ['dbData' => $comment]) ?>
				<?php endforeach ?>
			<?php endif ?>
		</div>

		<h4 class="contents-head"><?php echo __('コメントを送る') ?></h4>

		<?php echo $this->BcForm->create('BlogComment', ['url' => '/blog/blog_comments/add/' . $blogContent['BlogContent']['id'] . '/' . $post['BlogPost']['id'], 'id' => 'BlogCommentAddForm']) ?>
		<?php echo $this->BcForm->input('BlogComment.captcha_id', ['type' => 'hidden', 'value' => $captchaId]) ?>

		<table cellpadding="0" cellspacing="0" class="row-table-01">
			<tr>
				<th><?php echo $this->BcForm->label('BlogComment.name', __('お名前') . '・' . __('ニックネーム')) ?><span
						style="color:red">＊</span></th>
				<td><?php echo $this->BcForm->input('BlogComment.name', ['type' => 'text']) ?></td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('BlogComment.email', __('Eメール')) ?><span style="color:red">＊</span>
				</th>
				<td>
					<?php echo $this->BcForm->input('BlogComment.email', ['type' => 'text', 'size' => 30]) ?>&nbsp;
					<small>※ <?php echo __('Eメールは公開されません') ?></small>
				</td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('BlogComment.url', 'URL') ?></th>
				<td><?php echo $this->BcForm->input('BlogComment.url', ['type' => 'text', 'size' => 30]) ?></td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('BlogComment.message', __('コメント')) ?><span
						style="color:red">＊</span></th>
				<td><?php echo $this->BcForm->input('BlogComment.message', ['type' => 'textarea', 'rows' => 10, 'cols' => 52]) ?></td>
			</tr>
		</table>

		<?php if ($blogContent['BlogContent']['auth_captcha']): ?>
			<div class="auth-captcha clearfix">
				<img src="" alt="<?php echo __d('baser', '認証画像') ?>" class="auth-captcha-image" id="AuthCaptchaImage"
					 style="display:none"/>
				<?php $this->BcBaser->img('admin/captcha_loader.gif', ['alt' => 'Loading...', 'class' => 'auth-captcha-image', 'id' => 'CaptchaLoader']) ?>
				<?php echo $this->BcForm->text('BlogComment.auth_captcha') ?><br/>
				&nbsp;<?php echo __('画像の文字を入力してください') ?><br/>
			</div>
		<?php endif ?>

		<?php echo $this->BcForm->end(['label' => __('送信する'), 'id' => 'BlogCommentAddButton', 'class' => 'button']) ?>

		<div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>

	</div>
<?php endif ?>
