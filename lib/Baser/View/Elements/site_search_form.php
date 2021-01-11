<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] サイト内検索フォーム
 */
if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
$siteId = 0;
if (!empty($this->request->params['Site']['id'])) {
	$siteId = $this->request->params['Site']['id'];
}
if (!empty($this->passedArgs['num'])) {
	$url = ['plugin' => null, 'controller' => 'search_indices', 'action' => 'search', 'num' => $this->passedArgs['num']];
} else {
	$url = ['plugin' => null, 'controller' => 'search_indices', 'action' => 'search'];
}
$folders = $this->BcContents->getContentFolderList($siteId, ['excludeId' => $this->BcContents->getSiteRootId($siteId)]);
?>


<div class="section search-box">
	<h2><?php echo __d('baser', 'サイト内検索') ?></h2>
	<?php echo $this->BcForm->create('SearchIndex', ['type' => 'get', 'url' => $url]) ?>
	<?php if ($folders): ?>
		<?php echo $this->BcForm->label('SearchIndex.f', __d('baser', 'カテゴリ')) ?><br>
		<?php echo $this->BcForm->input('SearchIndex.f', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser', '指定しない')]) ?>
		<br>
	<?php endif ?>
	<?php echo $this->BcForm->input('SearchIndex.q', ['placeholder' => __d('baser', 'キーワード'), 'escape' => false]) ?>
	<?php echo $this->BcForm->hidden('SearchIndex.s', ['value' => $siteId]) ?>
	<?php echo $this->BcForm->submit(__d('baser', '検索'), ['div' => false, 'class' => 'submit_button']) ?>
	<?php echo $this->BcForm->end() ?>
</div>
