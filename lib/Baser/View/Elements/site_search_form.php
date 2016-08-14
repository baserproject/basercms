<?php
/**
 * [PUBLISH] サイト内検索フォーム
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
if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
if (!empty($this->passedArgs['num'])) {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search', 'num' => $this->passedArgs['num']);
} else {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search');
}
?>


<div class="section search-box">
	<?php echo $this->BcForm->create('SearchIndex', array('type' => 'get', 'url' => $url)) ?>
	<?php echo $this->BcForm->input('SearchIndex.q') ?>
	<?php echo $this->BcForm->hidden('SearchIndex.site_id', ['value' => 0]) ?>
	<?php echo $this->BcForm->submit('検索', array('div' => false, 'class' => 'submit_button button')) ?>
	<?php echo $this->BcForm->end() ?>
</div>