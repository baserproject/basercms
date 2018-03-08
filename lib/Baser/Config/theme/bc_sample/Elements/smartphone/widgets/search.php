<?php
/**
 * サイト内検索（スマホ用）
 * 呼出箇所：ウィジェット
 */
if (!empty($this->passedArgs['num'])) {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search', 'num' => $this->passedArgs['num']);
} else {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search');
}
?>


<div class="widget widget-site-search widgetsite-search-<?php echo $id ?>">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php echo $this->BcForm->create('SearchIndex', array('type' => 'get', 'url' => $url)) ?>
	<?php echo $this->BcForm->hidden('SearchIndex.s', ['value' => 0]) ?>
	<br>
	<?php echo $this->BcForm->input('SearchIndex.q') ?>
	<?php echo $this->BcForm->submit(__('検索'), array('div' => false, 'class' => 'button-small')) ?>
	<?php echo $this->BcForm->end() ?>
</div>
