<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

use BaserCore\View\BcAdminAppView;

/**
 * [ADMIN] 統合コンテンツ一覧
 *
 * @var BcAdminAppView $this
 * @var array $contents
 * @var bool $isContentDeletable
 */
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
    $alias = $content->alias_id? true : false;
    $open = false;
    $items = $this->BcContents->getConfig('items');
    if (!empty($items[$content->type]['icon'])) {
      if (!empty($items[$content->type]['url']['icon'])) {
        $icon = $items[$content->type]['url']['icon'];
      } else {
        $icon = $items[$content->type]['icon'];
      }
    } else {
      $icon = $items['Default']['url']['icon'] ?? '';
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
    <?php
    // data-jstree は JSON として解釈されるため、値（特に name / contentTitle）を
    // JSON エンコードしたうえで属性値としてエスケープする（格納型 XSS 対策）
    $jstreeData = [
        'icon' => $icon,
        'name' => rawurldecode((string)$content->name),
        'type' => $treeItemType,
        'status' => (string)(bool)$status,
        'alias' => (string)(bool)$alias,
        'related' => (string)(bool)$this->BcContents->isSiteRelated($content),
        'contentId' => (string)$content->id,
        'contentParentId' => (string)$parentId,
        'contentEntityId' => (string)$content->entity_id,
        'contentSiteId' => (string)$content->site_id,
        'contentFullUrl' => (string)$fullUrl,
        'contentType' => (string)$content->type,
        'contentAliasId' => (string)$content->alias_id,
        'contentPlugin' => (string)$content->plugin,
        'contentTitle' => (string)$content->title,
        'contentSiteRoot' => (string)(bool)$content->site_root,
        'editDisabled' => (string)$editDisabled,
        'manageDisabled' => (string)$manageDisabled,
        'deleteDisabled' => (string)!$isContentDeletable,
    ];
    ?>
    <li id="node-<?= $content->id ?>" data-jstree='<?= h(json_encode($jstreeData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>'class="<?php if ($open): ?>jstree-open<?php endif ?> display-none"
    ><?php
      echo h($content->title);
      if (!empty($content->children)) {
        $this->BcBaser->element('Contents/index_list_tree', ['contents' => $content->children]);
      }
      ?></li>
  <?php endforeach ?>
</ul>

