<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * ローカルナビ
 *
 * $this->BcBaser->widgetArea('ウィジェットエリアNO') で呼び出す
 * 管理画面で設定されたウィジェットエリアNOは、 $widgetArea で参照できる
 *
 * @var BcAppView $this
 * @var int $id ウィジェットID
 * @var book $use_title タイトルの利用可否
 */
if(empty($this->request->params['Content']) || ($this->request->params['controller'] === 'search_indices' && $this->request->params['action'] === 'search')) {
	return;
}
if($this->request->params['Content']['type'] == 'ContentFolder') {
	$parentId = $this->request->params['Content']['id'];
	$title = $this->request->params['Content']['title'];
} else {
	$parent = $this->BcContents->getParent($this->request->params['Content']['id']);
	$parentId = $parent['Content']['id'];
	$title = $parent['Content']['title'];
	if($parent['Content']['site_root']) {
		return;
	}
}
?>


<div class="bs-widget bs-widget-local-navi bs-widget-local-navi-<?php echo $id ?>">
	<?php if ($use_title): ?>
		<h2 class="bs-widget-head"><?php echo h($title) ?></h2>
	<?php endif ?>
	<!-- /Elements/page_list.php -->
	<?php $this->BcBaser->contentsMenu($parentId, 1, $this->request->params['Content']['id']) ?>
</div>
