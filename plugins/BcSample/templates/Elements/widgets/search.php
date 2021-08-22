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
 * サイト検索フォーム
 * 呼出箇所：ウィジェット
 *
 * $this->BcBaser->widgetArea('ウィジェットエリアNO') で呼び出す
 * 管理画面で設定されたウィジェットエリアNOは、 $widgetArea で参照できる
 *
 * @var BcAppView $this
 * @var int $id ウィジェットID
 */

if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
$siteId = 0;
if(!empty($this->request->params['Site']['id'])) {
	$siteId = $this->request->params['Site']['id'];
}
if (!empty($this->passedArgs['num'])) {
	$url = ['plugin' => null, 'controller' => 'search_indices', 'action' => 'search', 'num' => $this->passedArgs['num']];
} else {
	$url = ['plugin' => null, 'controller' => 'search_indices', 'action' => 'search'];
}
$folders = $this->BcContents->getContentFolderList($siteId, ['excludeId' => $this->BcContents->getSiteRootId($siteId)]);
?>


<div class="bs-widget bs-widget-search-box bs-widget-search-box-<?php echo $id ?>">
	<h2 class="bs-widget-head"><?php echo __('サイト内検索') ?></h2>
	<div class="bs-widget-form">
	<?php echo $this->BcForm->create('SearchIndex', ['type' => 'get', 'url' => $url]) ?>
	<?php if($folders): ?>
		<?php echo $this->BcForm->label('SearchIndex.f', __('カテゴリ')) ?><br>
		<?php echo $this->BcForm->input('SearchIndex.f', ['type' => 'select', 'options' => $folders, 'empty' => __('指定しない')]) ?><br>
	<?php endif ?>
	<?php echo $this->BcForm->input('SearchIndex.q', ['placeholder' => __('キーワード'), 'escape' => false]) ?>
	<?php echo $this->BcForm->hidden('SearchIndex.s', ['value' => $siteId]) ?>
	<?php echo $this->BcForm->submit(__('検索'), ['div' => false, 'class' => 'bs-button-small']) ?>
	<?php echo $this->BcForm->end() ?>
	</div>
</div>
