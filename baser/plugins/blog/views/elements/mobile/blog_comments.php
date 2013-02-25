<?php
/* SVN FILE: $Id$ */
/**
 * [MOBILE] ブログコメント一覧
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
<?php if($blogContent['BlogContent']['comment_use']): ?>

<div id="BlogComment">
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">この記事へのコメント</span> </div>
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<?php if(!empty($commentMessage)): ?>
	<span style="color:red;"><?php echo $commentMessage ?></span><br />
	<br />
	<?php endif ?>
	<?php if(!empty($post['BlogComment'])): ?>
	<div id="BlogCommentList">
		<?php foreach($post['BlogComment'] as $comment): ?>
		<?php $bcBaser->element('blog_comment',array('dbData'=>$comment)) ?>
		<?php endforeach ?>
	</div>
	<?php endif ?>
	<br />
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<div style="text-align:center;background-color:#8ABE08;"> <span style="color:white;">コメントを送る</span> </div>
	<hr size="1" style="width:100%;height:1px;margin:2px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<?php echo $bcForm->create('BlogComment',array('url'=>'/'.Configure::read('BcAgent.mobile.alias').'/'.$blogContent['BlogContent']['name'].'/archives/'.$post['BlogPost']['no'].'#BlogComment')) ?> 
	<?php echo $bcForm->label('BlogComment.name','お名前') ?><br />
	<?php echo $bcForm->text('BlogComment.name') ?><br />
	<span style="color:red;"><?php echo $bcForm->error('BlogComment.name') ?></span> 
	<?php echo $bcForm->label('BlogComment.email','Eメール') ?>&nbsp;<small>※ 非公開</small><br />
	<?php echo $bcForm->text('BlogComment.email',array('size'=>30)) ?><br />
	<span style="color:red;"><?php echo $bcForm->error('BlogComment.email') ?></span> 
	<?php echo $bcForm->label('BlogComment.url','URL') ?><br />
	<?php echo $bcForm->text('BlogComment.url',array('size'=>30)) ?><br />
	<span style="color:red;"><?php echo $bcForm->error('BlogComment.url') ?></span> 
	<?php echo $bcForm->label('BlogComment.message','コメント') ?><br />
	<?php echo $bcForm->textarea('BlogComment.message',array('rows'=>6,'cols'=>26)) ?> 
	<span style="color:red;"><?php echo $bcForm->error('BlogComment.message') ?></span> 
	<?php echo $bcForm->end(array('label'=>'　　送信する　　','id'=>'BlogCommentAddButton')) ?>
	<div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>
</div>
<?php endif ?>