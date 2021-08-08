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

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use BaserCore\View\BcAdminAppView;

/**
 * [ADMIN] 統合コンテンツ一覧
 *
 * @var BcAdminAppView $this
 * @var array $datas
 */

$PermissionModel = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
$deleteDisabled = false;
// loginUserGroup
// if (!$PermissionModel->check('/' . BcUtil::getPrefix() . '/contents/delete', $this->viewVars['user']['user_group_id'])) {
//   $deleteDisabled = true;
// }
?>
<ul>
  <?php foreach($datas as $data): ?>
    <?php
    $treeItemType = 'default';
    if ($data->type == 'ContentFolder') {
      $treeItemType = 'folder';
    }

    $fullUrl = $this->BcContents->getUrl($data->url, true, $data['Site']['use_subdomain']);
    $parentId = $data->parent_id;
    $alias = false;
    $open = false;
    if (!empty($this->BcContents->settings[$data->type]['icon'])) {
      if (!empty($this->BcContents->settings[$data->type]['url']['icon'])) {
        $icon = $this->BcContents->settings[$data->type]['url']['icon'];
      } else {
        $icon = $this->BcContents->settings[$data->type]['icon'];
      }
    } else {
      $icon = $this->BcContents->settings['Default']['url']['icon'];
    }
    if ($data->alias_id) {
      $alias = true;
    }
    $status = $this->BcContents->isAllowPublish($data, true);
    if ($data->site_root) {
      $open = true;
    }
    if ($alias) {
      $editDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'edit', $data->entity_id);
      $manageDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'manage', $data->entity_id);
    } else {
      $editDisabled = !$this->BcContents->isActionAvailable($data->type, 'edit', $data->entity_id);
      $manageDisabled = !$this->BcContents->isActionAvailable($data->type, 'manage', $data->entity_id);
    }
    ?>
    <li id="node-<?= $data->id ?>" data-jstree='{
	"icon":"<?php echo $icon ?>",
	"name":"<?php echo urldecode($data->name) ?>",
	"type":"<?php echo $treeItemType ?>",
	"status":"<?php echo (bool)$status ?>",
	"alias":"<?php echo (bool)$alias ?>",
	"related":"<?php echo (bool)$this->BcContents->isSiteRelated($data) ?>",
	"contentId":"<?php echo $data->id ?>",
	"contentParentId":"<?php echo $parentId ?>",
	"contentEntityId":"<?php echo $data->entity_id ?>",
	"contentSiteId":"<?php echo $data->site_id ?>",
	"contentFullUrl":"<?php echo $fullUrl ?>",
	"contentType":"<?php echo $data->type ?>",
	"contentAliasId":"<?php echo $data->alias_id ?>",
	"contentPlugin":"<?php echo $data->plugin ?>",
	"contentTitle":"<?php echo h(str_replace('"', '\"', $data->title)) ?>",
	"contentSiteRoot":"<?php echo (bool)$data->site_root ?>",
	"editDisabled":"<?php echo $editDisabled ?>",
	"manageDisabled":"<?php echo $manageDisabled ?>",
	"deleteDisabled":"<?php echo $deleteDisabled ?>"
}'<?php if ($open): ?> class="jstree-open"<?php endif ?>
    ><?php
      echo h($data->title);
      if (!empty($data['children'])) {
        $this->BcBaser->element('Contents/index_list_tree', ['datas' => $data['children']]);
      }
      ?></li>
  <?php endforeach ?>
</ul>

