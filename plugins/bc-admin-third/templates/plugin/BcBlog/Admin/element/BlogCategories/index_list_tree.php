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

/**
 * [ADMIN] ブログカテゴリ 一覧 ツリー（再帰）
 * @var BcBlog\View\BlogAdminAppView $this
 * @var iterable $blogCategories 親子ネスト（children）付きのカテゴリ
 * @var BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
?>
<ul>
  <?php foreach($blogCategories as $blogCategory): ?>
    <?php
    $editUrl = $this->BcBaser->getUrl(['action' => 'edit', $blogContent->id, $blogCategory->id]);
    $previewUrl = $this->Blog->getCategoryUrl($blogCategory->id);
    ?>
    <li id="blog-category-<?php echo $blogCategory->id ?>" data-jstree='{
      "icon":"bca-icon--folder",
      "categoryId":"<?php echo $blogCategory->id ?>",
      "parentId":"<?php echo $blogCategory->parent_id ?>",
      "status":"<?php echo (bool)$blogCategory->status ?>",
      "editUrl":"<?php echo $editUrl ?>",
      "previewUrl":"<?php echo $previewUrl ?>"
    }' class="jstree-open"><?php
      echo h($blogCategory->title);
      if (!empty($blogCategory->children)) {
        $this->BcBaser->element('BlogCategories/index_list_tree', ['blogCategories' => $blogCategory->children, 'blogContent' => $blogContent]);
      }
      ?></li>
  <?php endforeach ?>
</ul>
