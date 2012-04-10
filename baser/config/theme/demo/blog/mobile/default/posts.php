<?php
/**
 * [MOBILE] タイトル一覧
 */
?>

<?php if(!empty($posts)): ?>
	<?php foreach($posts as $key => $post): ?>
<span style="color:#8ABE08">◆</span>&nbsp;<?php $blog->postDate($post, 'y.m.d') ?><br />
<?php $blog->postTitle($post) ?>
<hr size="1" style="width:100%;height:1px;margin:5px 0;padding:0;color:#CCCCCC;background:#CCCCCC;border:1px solid #CCCCCC;" />
	<?php endforeach; ?>
<?php else: ?>
<p style="text-align:center">ー</p>
<?php endif; ?>