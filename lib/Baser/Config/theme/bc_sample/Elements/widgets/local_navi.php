<?php
/**
 * [PUBLISH] ローカルナビゲーションウィジェット
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * $this->BcBaser->widgetArea('ウィジェットエリアNO') で呼び出す
 * 管理画面で設定されたウィジェットエリアNOは、 $widgetArea で参照できる
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