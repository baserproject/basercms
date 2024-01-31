<?php
/**
 * サイト内検索フォーム
 */
if ($this->getRequest()->is('maintenance')) {
	return;
}
if ($this->getRequest()->getQuery('limit')) {
	$url = array('plugin' => 'BcSearchIndex', 'controller' => 'SearchIndexes', 'action' => 'search', 'num' => $this->getRequest()->getQuery('limit'));
} else {
	$url = array('plugin' => 'BcSearchIndex', 'controller' => 'SearchIndexes', 'action' => 'search');
}
if(!isset($searchIndexesFront)) $searchIndexesFront = null;
?>


<div class="section search-box">
	<?php echo $this->BcForm->create($searchIndexesFront, array('type' => 'get', 'url' => $url)) ?>
	<?php echo $this->BcForm->control('q', ['escape' => false]) ?>
	<?php echo $this->BcForm->hidden('site_id', ['value' => $this->getRequest()->getAttribute('currentSite')->id]) ?>
	<?php echo $this->BcForm->submit('検索', array('div' => false, 'class' => 'submit_button bs-button')) ?>
	<?php echo $this->BcForm->end() ?>
</div>
