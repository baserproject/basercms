<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @unitTest
 */
$this->BcBaser->js('admin/libs/jquery.baseUrl.js', true, ['once' => true, 'defer']);
$this->BcBaser->js('admin/libs/jquery.bcUtil.js', true, ['once' => true, 'defer']);
$this->BcBaser->js('admin/libs/jquery.bcToken.js', true, ['once' => true, 'defer']);
$this->BcBaser->js('BcBlog.blog_comments_scripts.js', true, [
	'defer',
	'once' => true,
	'id' => 'BlogCommentsScripts',
	'data-alertMessageName' => __('お名前を入力してください'),
	'data-alertMessageComment' => __('コメントを入力してください'),
	'data-alertMessageAuthImage' => __('画像の文字を入力してください'),
	'data-alertMessageAuthComplate' => __('送信が完了しました。送信された内容は確認後公開させて頂きます。'),
	'data-alertMessageComplate' => __('コメントの送信が完了しました。'),
	'data-alertMessageError' => __('コメントの送信に失敗しました。入力内容を見なおしてください。'),
]);
?>


<script>
	authCaptcha = <?php echo $blogContent['BlogContent']['auth_captcha']? 'true' : 'false'; ?>;
	commentApprove = <?php echo $blogContent['BlogContent']['comment_approve']? 'true' : 'false'; ?>;
</script>
