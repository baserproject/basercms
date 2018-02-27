<?php
/**
 * ブログコメント一覧
 * 呼出箇所：ブログ記事詳細
 */
?>

<?php echo $this->element('blog_comments_scripts'); ?>

<?php $captchaId = mt_rand(0, 99999999) ?>
<div id="BlogCommentCaptchaUrl" style="display:none"><?php echo $this->BcBaser->getUrl('/blog/blog_comments/captcha/' . $captchaId) ?></div>
<div id="BlogCommentGetTokenUrl" style="display:none"><?php echo $this->BcBaser->getUrl('/blog/blog_comments/get_token') ?></div>

<?php if ($blogContent['BlogContent']['comment_use']): ?>
	<div id="BlogComment">

		<h4 class="contents-head"><?php echo __('この記事へのコメント') ?></h4>

		<div id="BlogCommentList">
			<?php if (!empty($post['BlogComment'])): ?>
				<?php foreach ($post['BlogComment'] as $comment): ?>
					<!-- /Elements/blog_comment.php -->
					<?php $this->BcBaser->element('blog_comment', array('dbData' => $comment)) ?>
				<?php endforeach ?>
			<?php endif ?>
		</div>

		<h4 class="contents-head"><?php echo __('コメントを送る') ?></h4>

		<?php echo $this->BcForm->create('BlogComment', array('url' => '/blog/blog_comments/add/' . $blogContent['BlogContent']['id'] . '/' . $post['BlogPost']['id'], 'id' => 'BlogCommentAddForm')) ?>
		<?php echo $this->BcForm->input('BlogComment.captcha_id', ['type' => 'hidden', 'value' => $captchaId]) ?>
		
		<table cellpadding="0" cellspacing="0" class="row-table-01">
			<tr>
				<th><?php echo $this->BcForm->label('BlogComment.name', __('お名前') . '.' . __('ニックネーム')) ?><span style="color:red">＊</span></th>
				<td><?php echo $this->BcForm->input('BlogComment.name', array('type' => 'text')) ?></td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('BlogComment.email', __('Eメール')) ?><span style="color:red">＊</span></th>
				<td>
					<?php echo $this->BcForm->input('BlogComment.email', array('type' => 'text', 'size' => 30)) ?>&nbsp;
					<br><small>※ <?php echo __('Eメールは公開されません') ?></small>
				</td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('BlogComment.url', 'URL') ?></th>
				<td><?php echo $this->BcForm->input('BlogComment.url', array('type' => 'text', 'size' => 30)) ?></td>
			</tr>
			<tr>
				<th><?php echo $this->BcForm->label('BlogComment.message', __('コメント')) ?><span style="color:red">＊</span></th>
				<td><?php echo $this->BcForm->input('BlogComment.message', array('type' => 'textarea', 'rows' => 10, 'cols' => 52)) ?></td>
			</tr>
		</table>

		<?php if ($blogContent['BlogContent']['auth_captcha']): ?>
			<div class="auth-captcha clearfix">
				<img src="" alt="<?php echo __('認証画象') ?>" class="auth-captcha-image" id="AuthCaptchaImage" style="display:none">
				<?php $this->BcBaser->img('admin/captcha_loader.gif', array('alt' => 'Loading...', 'class' => 'auth-captcha-image', 'id' => 'CaptchaLoader')) ?>
				<?php echo $this->BcForm->text('BlogComment.auth_captcha') ?><br>
				&nbsp;<?php echo __('画像の文字を入力してください') ?><br>
			</div>
		<?php endif ?>

		<?php echo $this->BcForm->end(array('label' => __('送信する'), 'id' => 'BlogCommentAddButton', 'class' => 'button')) ?>

		<div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>

	</div>
<?php endif ?>