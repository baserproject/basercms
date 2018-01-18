<?php
/**
 * [PUBLISH] サイト内検索フォームウィジェット
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

if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
if (!empty($this->passedArgs['num'])) {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search', 'num' => $this->passedArgs['num']);
} else {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search');
}
$folders = $this->BcContents->getContentFolderList($this->request->params['Site']['id'], ['excludeId' => $this->BcContents->getSiteRootId($this->request->params['Site']['id'])]);
?>


<div class="widget widget-search-box widget-search-box-<?php echo $id ?>">
	<h2><?php echo __('サイト内検索') ?></h2>
	<?php echo $this->BcForm->create('SearchIndex', ['type' => 'get', 'url' => $url]) ?>
	<?php if($folders): ?>
		<?php echo $this->BcForm->label('SearchIndex.f', __('カテゴリ')) ?><br>
		<?php echo $this->BcForm->input('SearchIndex.f', ['type' => 'select', 'options' => $folders, 'empty' => __('指定しない'), 'escape' => false]) ?><br>
	<?php endif ?>
	<?php echo $this->BcForm->input('SearchIndex.q', ['placeholder' => __('キーワード')]) ?>
	<?php echo $this->BcForm->hidden('SearchIndex.s', ['value' => $this->request->params['Site']['id']]) ?>
	<?php echo $this->BcForm->submit(__('検索'), array('div' => false, 'class' => 'submit_button')) ?>
	<?php echo $this->BcForm->end() ?>
</div>