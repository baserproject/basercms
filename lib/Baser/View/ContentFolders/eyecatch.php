<h1><?php echo $this->request->params['Content']['title'] ?></h1>
<?php if($children): ?>
<ul class="eyecatch-list clearfix">
	<?php foreach($children as $child): ?>
		<li>
			<?php $this->BcBaser->link($this->BcUpload->uploadImage('Content.eyecatch', $child['Content']['eyecatch'], array(
				'imgsize' => 'thumb',
				'link'		=> false,
				'noimage'	=> 'admin/noimage.png'
			)), $child['Content']['url']) ?>
			<p><?php $this->BcBaser->link($child['Content']['title'], $child['Content']['url']) ?></p>
		</li>
	<?php endforeach ?>
</ul>
<?php endif ?>
