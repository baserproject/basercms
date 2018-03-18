<?php
/**
 * ブログコメント送信メール（コメント送信者）
 * 呼出箇所：ブログコメント（コメント送信時）
 */
?>

                                           <?php echo date('Y-m-d H:i:s') ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　<?php echo __d('baser', 'コメントが投稿されました')?>　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php echo sprintf(__d('baser', '%sさんが、「%s」にコメントしました。'), $BlogComment['name'], $BlogPost['name'])?>　
<?php echo $this->BcBaser->getUri($Content['url'] . 'archives/' . $BlogPost['no'], false) ?>　
 
<?php echo ($BlogComment['message']) ?>　
　
　
　
　
　

