<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
use BaserCore\View\BcAdminAppView;

/**
 * [ADMIN] 統合コンテンツ一覧
 *
 * @var BcAdminAppView $this
 * @var Query $contents
 */
$deleteDisabled = !$this->BcAdminContent->isContentDeletable();
?>
<ul>
  <?php foreach($contents as $content): ?>
    <?php
    $treeItemType = 'default';
    if ($content->type == 'ContentFolder') {
      $treeItemType = 'folder';
    }

    $fullUrl = $this->BcContents->getUrl($content->url, true, $content->site->use_subdomain);
    $parentId = $content->parent_id;
    $alias = false;
    $open = false;
    $settings = $this->BcContents->getConfig('settings');
    if (!empty($settings[$content->type]['icon'])) {
      if (!empty($settings[$content->type]['url']['icon'])) {
        $icon = $settings[$content->type]['url']['icon'];
      } else {
        $icon = $settings[$content->type]['icon'];
      }
    } else {
      // TODO: iconがない場合があるため見直す
      $icon = $settings['Default']['url']['icon'] ?? '';
    }
    if ($content->alias_id) {
      $alias = true;
    }
    $status = $this->BcContents->isAllowPublish($content, true);
    if ($content->site_root) {
      $open = true;
    }
    if ($alias) {
      $editDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'edit', $content->entity_id);
      $manageDisabled = !$this->BcContents->isActionAvailable('ContentAlias', 'manage', $content->entity_id);
    } else {
      $editDisabled = !$this->BcContents->isActionAvailable($content->type, 'edit', $content->entity_id);
      $manageDisabled = !$this->BcContents->isActionAvailable($content->type, 'manage', $content->entity_id);
    }
    ?>
    <li id="node-<?= $content->id ?>" data-jstree='{
	"icon":"<?php echo $icon ?>",
	"name":"<?php echo urldecode($content->name) ?>",
	"type":"<?php echo $treeItemType ?>",
	"status":"<?php echo (bool)$status ?>",
	"alias":"<?php echo (bool)$alias ?>",
	"related":"<?php echo (bool)$this->BcContents->isSiteRelated($content) ?>",
	"contentId":"<?php echo $content->id ?>",
	"contentParentId":"<?php echo $parentId ?>",
	"contentEntityId":"<?php echo $content->entity_id ?>",
	"contentSiteId":"<?php echo $content->site_id ?>",
	"contentFullUrl":"<?php echo $fullUrl ?>",
	"contentType":"<?php echo $content->type ?>",
	"contentAliasId":"<?php echo $content->alias_id ?>",
	"contentPlugin":"<?php echo $content->plugin ?>",
	"contentTitle":"<?php echo h(str_replace('"', '\"', $content->title)) ?>",
	"contentSiteRoot":"<?php echo (bool)$content->site_root ?>",
	"editDisabled":"<?php echo $editDisabled ?>",
	"manageDisabled":"<?php echo $manageDisabled ?>",
	"deleteDisabled":"<?php echo $deleteDisabled ?>"
}'<?php if ($open): ?> class="jstree-open"<?php endif ?>
    ><?php
      echo h($content->title);
      if (!empty($content->children)) {
        $this->BcBaser->element('Contents/index_list_tree', ['contents' => $content->children]);
      }
      ?></li>
  <?php endforeach ?>
</ul>

