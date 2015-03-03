<?php
/**
 * [MOBILE] ブログコメント一覧
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>
<?php if ($blogContent['BlogContent']['comment_use']): ?>

	<div id="BlogComment">
		<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
		<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">この記事へのコメント</span> </div>
		<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
		<?php if (!empty($commentMessage)): ?>
			<span style="color:red;"><?php echo $commentMessage ?></span><br />
			<br />
		<?php endif ?>
		<?php if (!empty($post['BlogComment'])): ?>
			<div id="BlogCommentList">
				<?php foreach ($post['BlogComment'] as $comment): ?>
					<?php $this->BcBaser->element('blog_comment', array('dbData' => $comment)) ?>
				<?php endforeach ?>
			</div>
		<?php endif ?>
		<br />
		<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
		<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">コメントを送る</span> </div>
		<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
		<?php echo $this->BcForm->create('BlogComment', array('url' => '/' . Configure::read('BcAgent.mobile.alias') . '/' . $blogContent['BlogContent']['name'] . '/archives/' . $post['BlogPost']['no'] . '#BlogComment')) ?> 
		<?php echo $this->BcForm->label('BlogComment.name', 'お名前') ?><br />
		<?php echo $this->BcForm->text('BlogComment.name') ?><br />
		<span style="color:red;"><?php echo $this->BcForm->error('BlogComment.name') ?></span> 
		<?php echo $this->BcForm->label('BlogComment.email', 'Eメール') ?>&nbsp;<small>※ 非公開</small><br />
		<?php echo $this->BcForm->text('BlogComment.email', array('size' => 30)) ?><br />
		<span style="color:red;"><?php echo $this->BcForm->error('BlogComment.email') ?></span> 
		<?php echo $this->BcForm->label('BlogComment.url', 'URL') ?><br />
		<?php echo $this->BcForm->text('BlogComment.url', array('size' => 30)) ?><br />
		<span style="color:red;"><?php echo $this->BcForm->error('BlogComment.url') ?></span> 
		<?php echo $this->BcForm->label('BlogComment.message', 'コメント') ?><br />
		<?php echo $this->BcForm->textarea('BlogComment.message', array('rows' => 6, 'cols' => 26)) ?> 
		<span style="color:red;"><?php echo $this->BcForm->error('BlogComment.message') ?></span> 
		<?php echo $this->BcForm->end(array('label' => '　　送信する　　', 'id' => 'BlogCommentAddButton')) ?>
		<div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>
	</div>
<?php endif ?>
