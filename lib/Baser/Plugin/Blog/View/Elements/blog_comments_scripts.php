<?php $this->BcBaser->js('admin/libs/jquery.baseUrl.js', false, ['once' => true]); ?>
<?php $this->BcBaser->js('admin/libs/jquery.bcUtil.js', false, ['once' => true]); ?>
<?php $this->BcBaser->js('admin/libs/jquery.bcToken.js', false, ['once' => true]); ?>
<?php $this->BcBaser->js('Blog.blog_comments_scripts.js', false, ['once' => true]); ?>
<div id="BaseUrl" style="display: none"><?php echo $this->request->base; ?></div>

<script>
	authCaptcha = <?php echo $blogContent['BlogContent']['auth_captcha'] ? 'true' : 'false'; ?>;
	commentApprove = <?php echo $blogContent['BlogContent']['comment_approve'] ? 'true' : 'false'; ?>;

	$(function() {
		loadAuthCaptcha();
		$("#BlogCommentAddButton").click(function() {
			sendComment();
			return false;
		});
	});
</script>
