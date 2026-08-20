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
 * [ADMIN] ブログカテゴリ 一覧 ツリー表示
 * @var BcBlog\View\BlogAdminAppView $this
 * @var \Cake\Datasource\ResultSetInterface $blogCategories
 * @var BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<?php if ($blogCategories->count()): ?>
  <div id="BlogCategoryTreeList">
    <?php $this->BcBaser->element('BlogCategories/index_list_tree', ['blogCategories' => $blogCategories, 'blogContent' => $blogContent]) ?>
  </div>
<?php else: ?>
  <div class="tree-empty"><?php echo __d('baser_core', 'データが登録されていません。') ?></div>
<?php endif ?>
