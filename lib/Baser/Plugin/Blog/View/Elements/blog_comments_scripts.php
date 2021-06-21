<?php $this->BcBaser->js('admin/libs/jquery.baseUrl.js', true, ['once' => true, 'defer']); ?>
<?php $this->BcBaser->js('admin/libs/jquery.bcUtil.js', true, ['once' => true, 'defer']); ?>
<?php $this->BcBaser->js('admin/libs/jquery.bcToken.js', true, ['once' => true, 'defer']); ?>
<?php $this->BcBaser->js('Blog.blog_comments_scripts.js', true, [
	'defer',
	'once' => true,
	'id' => 'BlogCommentsScripts',
	'data-alertMessageName' => __('お名前を入力してください'),
	'data-alertMessageComment' => __('コメントを入力してください'),
	'data-alertMessageAuthImage' => __('画像の文字を入力してください'),
	'data-alertMessageAuthComplate' => __('送信が完了しました。送信された内容は確認後公開させて頂きます。'),
	'data-alertMessageComplate' => __('コメントの送信が完了しました。'),
	'data-alertMessageError' => __('コメントの送信に失敗しました。入力内容を見なおしてください。'),
]); ?>
<div id="BaseUrl" style="display: none"><?php echo h($this->request->base); ?></div>

<script>
	authCaptcha = <?php echo $blogContent['BlogContent']['auth_captcha']? 'true' : 'false'; ?>;
	commentApprove = <?php echo $blogContent['BlogContent']['comment_approve']? 'true' : 'false'; ?>;
</script>
