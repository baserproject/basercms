<?php
$iconType = 'Content';
$treeItemType = 'default';
$type = $data['Content']['type'];
$fullUrl = $this->BcContents->getUrl($data['Content']['url'], true, $data['Site']['use_subdomain']);
$parentId = $data['Content']['parent_id'];
$alias = false;
$open = false;
switch ($type) {
	case 'Page':
		$iconPath = '/img/admin/icon_page.png';
		break;
	case 'ContentFolder':
		$treeItemType = 'folder';
		$iconPath = '/img/admin/icon_folder.png';
		break;
	default:
		$iconPath = $this->BcContents->settings[$type]['icon'];
		break;
}
if($data['Content']['alias_id']) {
	$alias = true;
}
$status = $this->BcContents->isAllowPublish($data);
if(in_array($data['Content']['parent_id'], array(0,1))) {
	$open = true;
}
$related = false;
if(($data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['alias_id']) ||
	$data['Site']['relate_main_site'] && $data['Content']['main_site_content_id'] && $data['Content']['type'] == 'ContentFolder') {
	$related = true;
}
?>


<li id="node-<?php echo $data['Content']['id'] ?>" data-jstree='{
	"icon":"<?php echo $iconPath ?>",
	"type":"<?php echo $treeItemType ?>",
	"status":"<?php echo $status ?>",
	"alias":"<?php echo $alias ?>",
	"related":"<?php echo $related ?>",
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
		<?php $this->BcBaser->element('admin/contents/index_list', array('datas' => $data['children'])) ?>
	<?php endif ?>
</li>