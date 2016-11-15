<?php
/**
 * ローカルナビ（スマホ用）
 * 呼出箇所：ウィジェット
 */
if(empty($this->request->params['Content'])) {
	return;
}
if($this->request->params['Content']['type'] == 'ContentFolder') {
	$parentId = $this->request->params['Content']['id'];
	$title = $this->request->params['Content']['title'];
} else {
	$parent = $this->BcContents->getParent($this->request->params['Content']['id']);
	$parentId = $parent['Content']['id'];
	$title = $parent['Content']['title'];
}
if(@$parent['Content']['site_root']) {
	return;
}
?>


<div class="widget widget-local-navi widget-local-navi-<?php echo $id ?>">
	<?php if ($use_title): ?>
		<h2><?php echo h($title) ?></h2>
	<?php endif ?>
	<!-- /Elements/page_list.php -->
	<?php $this->BcBaser->contentsMenu($parentId, 1, $this->request->params['Content']['id']) ?>
</div>
