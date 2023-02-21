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
 * [ADMIN] ブログ記事 一覧　検索ボックス
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */
$blogCategories = $this->BcAdminForm->getControlSource('BcBlog.BlogPosts.blog_category_id', ['blogContentId' => $blogContent->id]);
$tags = $this->BcAdminForm->getControlSource('BcBlog.BlogPosts.blog_tag_id');
$users = $this->BcAdminForm->getControlSource("BcBlog.BlogPosts.user_id");
?>


<?php echo $this->BcAdminForm->create(null, ['novalidate' => true, 'type' => 'get', 'url' => ['action' => 'index', $blogContent->id]]) ?>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('title', __d('baser', 'タイトル'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('title', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => '30']) ?></span>
  <?php if ($blogCategories): ?>
    <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('blog_category_id', __d('baser', 'カテゴリー'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('blog_category_id', ['type' => 'select', 'options' => $blogCategories, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <?php endif ?>
  <?php if ($blogContent->tag_use && $tags): ?>
    <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('blog_tag_id', __d('baser', 'タグ'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('blog_tag_id', ['type' => 'select', 'options' => $tags, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <?php endif ?>
  <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('status', __d('baser', '公開状態'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('user_id', __d('baser', '作成者'), ['class' => 'bca-search__input-item-label']) ?>
    <?php echo $this->BcAdminForm->control('user_id', ['type' => 'select', 'options' => $users, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="button bca-search__btns">
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser', '検索'), ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-loading', 'data-bca-btn-type' => 'search']) ?>
  </div>
  <div class="bca-search__btns-item">
    <?php echo $this->BcAdminForm->button(__d('baser', 'クリア'), ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?>
  </div>
</div>
<?php echo $this->Form->end() ?>
