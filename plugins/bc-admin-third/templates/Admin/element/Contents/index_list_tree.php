<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] 統合コンテンツ一覧
 *
 * @var BcAppView $this
 * @var array $datas
 */

$PermissionModel = ClassRegistry::init('Permission');
$deleteDisabled = false;
if (!$PermissionModel->check('/' . Configure::read('Routing.prefixes.0') . '/contents/delete', $this->viewVars['user']['user_group_id'])) {
	$deleteDisabled = true;
}
?>


<ul>
	<?php foreach($datas as $data): ?>
		<?php
		$type = $data['Content']['type'];
		$treeItemType = 'default';
		if ($type == 'ContentFolder') {
			$treeItemType = 'folder';
		}
		$fullUrl = $this->BcContents->getUrl($data['Content']['url'], true, $data['Site']['use_subdomain']);
		$parentId = $data['Content']['parent_id'];
		$alias = false;
		$open = false;
		if (!empty($this->BcContents->settings[$type]['icon'])) {
			if (!empty($this->BcContents->settings[$type]['url']['icon'])) {
				$icon = $this->BcContents->settings[$type]['url']['icon'];
			} else {
				$icon = $this->BcContents->settings[$type]['icon'];
			}
		} else {
			$icon = $this->BcContents->settings['Default']['url']['icon'];
		}
		if ($data['Content']['alias_id']) {
			$alias = true;
		}
		$status = $this->BcContents->isAllowPublish($data, true);
		if ($data['Content']['site_root']) {
			$open = true;
		}
		if ($alias) {
			$editDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'edit', $data['Content']['entity_id']);
			$manageDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'manage', $data['Content']['entity_id']);
		} else {
			$editDisabled = !$this->BcContents->isActionAvailable($data['Content']['type'], 'edit', $data['Content']['entity_id']);
			$manageDisabled = !$this->BcContents->isActionAvailable($data['Content']['type'], 'manage', $data['Content']['entity_id']);
		}
		?>
		<li id="node-<?php echo $data['Content']['id'] ?>" data-jstree='{
	"icon":"<?php echo $icon ?>",
	"name":"<?php echo urldecode($data['Content']['name']) ?>",
	"type":"<?php echo $treeItemType ?>",
	"status":"<?php echo (bool)$status ?>",
	"alias":"<?php echo (bool)$alias ?>",
	"related":"<?php echo (bool)$this->BcContents->isSiteRelated($data) ?>",
	"contentId":"<?php echo $data['Content']['id'] ?>",
	"contentParentId":"<?php echo $parentId ?>",
	"contentEntityId":"<?php echo $data['Content']['entity_id'] ?>",
	"contentSiteId":"<?php echo $data['Content']['site_id'] ?>",
	"contentFullUrl":"<?php echo $fullUrl ?>",
	"contentType":"<?php echo $type ?>",
	"contentAliasId":"<?php echo $data['Content']['alias_id'] ?>",
	"contentPlugin":"<?php echo $data['Content']['plugin'] ?>",
	"contentTitle":"<?php echo h(str_replace('"', '\"', $data['Content']['title'])) ?>",
	"contentSiteRoot":"<?php echo (bool)$data['Content']['site_root'] ?>",
	"editDisabled":"<?php echo $editDisabled ?>",
	"manageDisabled":"<?php echo $manageDisabled ?>",
	"deleteDisabled":"<?php echo $deleteDisabled ?>"
}'<?php if ($open): ?> class="jstree-open"<?php endif ?>
		><?php
		echo h($data['Content']['title']);
		if (!empty($data['children'])) {
			$this->BcBaser->element('admin/contents/index_list_tree', ['datas' => $data['children']]);
		}
		?></li>
	<?php endforeach ?>
</ul>

