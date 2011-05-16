<?php
if(!empty($this->passedArgs['num'])) {
	$url = array('plugin' => null, 'controller' => 'contents', 'num' => $this->passedArgs['num']);
} else {
	$url = array('plugin' => null, 'controller' => 'contents');
}

?>
<div class="section search-box">
<?php echo $formEx->create('Content', array('type' => 'get', 'action' => 'search', 'url' => $url)) ?>
<?php if(unserialize($baser->siteConfig['content_categories'])) : ?>
<?php echo $formEx->input('Content.c', array('type' => 'select', 'options' => unserialize($baser->siteConfig['content_categories']), 'empty' => 'カテゴリ： 指定しない　')) ?>
<?php endif ?>
<?php echo $formEx->input('Content.q') ?>
<?php echo $formEx->submit('検索', array('div'=>false)) ?>
<?php echo $formEx->end() ?>
</div>