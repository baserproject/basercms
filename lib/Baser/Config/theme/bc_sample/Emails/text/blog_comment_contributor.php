<?php
/**
 * ブログコメント送信メール（コメント送信者）
 * 呼出箇所：ブログコメント（コメント送信時）
 */
?>

                                           <?php echo date('Y-m-d H:i:s') ?> 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　<?php echo __('コメントが投稿されました') ?>　◇◆ 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php echo $Content['name'] ?><?php echo __('さんが、') ?>
「<?php echo $BlogPost['name'] ?>」<?php echo __('にコメントしました。') ?>
<?php echo $this->BcBaser->getUri($Content['url'] . '/archives/' . $BlogPost['no'], false) ?>　
 
<?php echo ($BlogComment['message']) ?>　
　
　

