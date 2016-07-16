<h1><?php echo $this->content['title'] ?></h1>
<?php if($children): ?>
<ul>
	<?php foreach($children as $child): ?>
	<li><?php $this->BcBaser->link($child['Content']['title'], $child['Content']['url']) ?></li>
	<?php endforeach ?>
</ul>
<?php endif ?>
