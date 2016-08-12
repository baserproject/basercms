<?php
/**
 * ブログコメント送信メール（コメント送信者）
 * 呼出箇所：ブログコメント（コメント送信時）
 */
?>

                                           <?php echo date('Y-m-d H:i:s') ?> 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　コメントが投稿されました　◇◆ 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php echo $BlogComment['name'] ?>さんが、
「<?php echo $BlogPost['name'] ?>」にコメントしました。
<?php echo $this->BcBaser->getUri('/' . $BlogContent['name'] . '/archives/' . $BlogPost['no'], false) ?>　
 
<?php echo ($BlogComment['message']) ?>　
　
　

