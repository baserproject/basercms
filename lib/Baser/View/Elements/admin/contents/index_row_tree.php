<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
$type = $data['Content']['type'];
$treeItemType = 'default';
if($type == 'ContentFolder') {
	$treeItemType = 'folder';	
}
$fullUrl = $this->BcContents->getUrl($data['Content']['url'], true, $data['Site']['use_subdomain']);
$parentId = $data['Content']['parent_id'];
$alias = false;
$open = false;
if(!empty($this->BcContents->settings[$type]['icon'])) {
	$iconPath = $this->BcContents->settings[$type]['url']['icon'];	
} else {
	$iconPath = $this->BcContents->settings['Default']['url']['icon'];
}
if($data['Content']['alias_id']) {
	$alias = true;
}
$status = $this->BcContents->isAllowPublish($data, true);
if(in_array($data['Content']['parent_id'], array(0,1))) {
	$open = true;
}
?>


<li id="node-<?php echo $data['Content']['id'] ?>" data-jstree='{
	"icon":"<?php echo $iconPath ?>",
	"type":"<?php echo $treeItemType ?>",
	"status":"<?php echo $status ?>",
	"alias":"<?php echo $alias ?>",
	"related":"<?php echo $this->BcContents->isSiteRelated($data) ?>",
	"contentId":"<?php echo $data['Content']['id'] ?>",
	"contentParentId":"<?php echo $parentId ?>",
	"contentEntityId":"<?php echo $data['Content']['entity_id'] ?>",
	"contentSiteId":"<?php echo $data['Content']['site_id'] ?>",
	"contentFullUrl":"<?php echo $fullUrl ?>",
	"contentType":"<?php echo $type ?>",
	"contentAliasId":"<?php echo $data['Content']['alias_id'] ?>",
	"contentPlugin":"<?php echo $data['Content']['plugin'] ?>",
	"contentTitle":"<?php echo $data['Content']['title'] ?>",
	"contentSiteRoot":"<?php echo $data['Content']['site_root'] ?>"
}'<?php if($open): ?> class="jstree-open"<?php endif ?>>
	<span><?php echo $data['Content']['title'] ?></span>
	<?php if(!empty($data['children'])): ?>
		<?php $this->BcBaser->element('admin/contents/index_list_tree', array('datas' => $data['children'])) ?>
	<?php endif ?>
</li>