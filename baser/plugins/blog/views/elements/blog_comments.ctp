<?php
/* SVN FILE: $Id$ */
/**
 * ブログコメント一覧
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<script type="text/javascript">
    $(function(){
        $("#BlogCommentAddButton").click(function(){
            var msg = '';
            if(!$("#BlogCommentName").val()){
                msg += 'お名前を入力して下さい\n';
            }
            if(!$("#BlogCommentMessage").val()){
                msg += 'コメントを入力して下さい\n';
            }
            if(!msg){
                $.ajax({
                    url: $("#BlogCommentAddForm").attr('action'),
                    type: 'POST',
                    data: $("#BlogCommentAddForm").serialize(),
                    dataType: 'html',
                    beforeSend: function() {
                        $("#BlogCommentAddButton").attr('disabled', 'disabled');
                        $("#ResultMessage").hide('slide',{direction:"up"},500);
                    },
                    success: function(result){
                        $("#BlogCommentName").val('');
                        $("#BlogCommentEmail").val('');
                        $("#BlogCommentUrl").val('');
                        $("#BlogCommentMessage").val('');
                        var resultMessage = '';
                        <?php if($blogContent['BlogContent']['comment_approve']): ?>
                            resultMessage = '送信が完了しました。送信された内容は確認後公開させて頂きます。';
                        <?php else: ?>
                            var comment = $(result);
                            comment.hide();
                            $("#BlogCommentList").append(comment);
                            comment.show(500);
                            resultMessage = 'コメントの送信が完了しました。';
                        <?php endif ?>
                        $("#ResultMessage").html(resultMessage);
                        $("#ResultMessage").show('slide',{direction:"up"},500);
						
                    },
                    error: function(){
                        alert('コメントの送信に失敗しました。');
                    },
                    complete: function(xhr, textStatus) {
                        $("#BlogCommentAddButton").removeAttr('disabled');
                    }});
            }else{
                alert(msg);
            }
            return false;
        });
    });
</script>

<?php if($blogContent['BlogContent']['comment_use']): ?>
<div id="BlogComment">
	
    <h4 class="contents-head">この記事へのコメント</h4>
    <div id="BlogCommentList">
		<?php if(!empty($post['BlogComment'])): ?>
			<?php foreach($post['BlogComment'] as $comment): ?>
				<?php $baser->element('blog_comment',array('dbData'=>$comment)) ?>
			<?php endforeach ?>
		<?php endif ?>
    </div>
	 
    <h4 class="contents-head">コメントを送る</h4>
    <?php echo $formEx->create('BlogComment',array('url'=>array($blogContent['BlogContent']['id'],$post['BlogPost']['id']))) ?>
        <table cellpadding="0" cellspacing="0" class="row-table-01">
            <tr><th><?php echo $formEx->label('BlogComment.name','お名前') ?></th>
                <td><?php echo $formEx->text('BlogComment.name') ?></td></tr>
            <tr><th><?php echo $formEx->label('BlogComment.email','Eメール') ?></th>
                <td><?php echo $formEx->text('BlogComment.email',array('size'=>30)) ?>&nbsp;<small>※ メールは公開されません</small></td></tr>
            <tr><th><?php echo $formEx->label('BlogComment.url','URL') ?></th>
                <td><?php echo $formEx->text('BlogComment.url',array('size'=>30)) ?></td></tr>
            <tr><th><?php echo $formEx->label('BlogComment.message','コメント') ?></th>
                <td><?php echo $formEx->textarea('BlogComment.message',array('rows'=>10,'cols'=>60)) ?></td></tr>
        </table>
        
    <?php echo $formEx->end(array('label'=>'　　送信する　　','id'=>'BlogCommentAddButton')) ?>
    <div id="ResultMessage" class="message" style="display:none;text-align:center">&nbsp;</div>
</div>
<?php endif ?>