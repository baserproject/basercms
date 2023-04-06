<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * ローカルナビ
 *
 * 呼出箇所：ウィジェット
 * $this->BcBaser->widgetArea('ウィジェットエリアNO') で呼び出す
 * 管理画面で設定されたウィジェットエリアNOは、 $widgetArea で参照できる
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var int $id ウィジェットID
 * @var bool $use_title タイトルの利用可否
 */
$request = $this->getRequest();
if(empty($request->getAttribute('currentContent'))) return;
if($request->getParam('controller') === 'SearchIndexes' && $request->getParam('action') === 'search') return;
if($request->getAttribute('currentContent')->type === 'ContentFolder') {
	$parentId = $request->getAttribute('currentContent')->id;
	$title = $request->getAttribute('currentContent')->title;
} else {
	$parent = $this->BcBaser->getParentContent($request->getAttribute('currentContent')->id);
	$parentId = $parent->id;
	$title = $parent->title;
	if($parent->site_root) {
		return;
	}
}
?>


<div class="bs-widget bs-widget-local-navi bs-widget-local-navi-<?php echo $id ?>">
	<?php if ($use_title): ?>
		<h2 class="bs-widget-head"><?php echo h($title) ?></h2>
	<?php endif ?>
	<!-- /Elements/page_list.php -->
	<?php $this->BcBaser->contentsMenu($parentId, 1, $request->getAttribute('currentContent')->id) ?>
</div>
