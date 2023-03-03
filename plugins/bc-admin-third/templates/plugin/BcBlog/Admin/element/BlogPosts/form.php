<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 フォーム
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $post
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @var string $editor
 * @var array $editorOptions
 * @var string $editorEnterBr
 * @var array $users
 * @var array $categories
 * @var bool $hasNewCategoryAddablePermission
 * @var bool $hasNewTagAddablePermission
 * @var string $fullUrl
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->css('admin/ckeditor/editor', true);
$this->BcBaser->i18nScript([
  'alertMessage1' => __d('baser', 'ブログタグの追加に失敗しました。既に登録されていないか確認してください。'),
  'alertMessage2' => __d('baser', 'ブログタグの追加に失敗しました。'),
  'alertMessage3' => __d('baser', 'ブログカテゴリの追加に失敗しました。入力したブログカテゴリ名が既に登録されていないか確認してください。'),
  'alertMessage4' => __d('baser', 'ブログカテゴリの追加に失敗しました。')
]);
$this->BcBaser->js('BcBlog.admin/blog_posts/form.bundle', false, [
  'id' => 'AdminBlogBLogPostsEditScript',
  'data-fullurl' => $fullUrl,
  'data-previewurl' => \Cake\Routing\Router::url(["plugin" => "BaserCore", "controller" => "preview", "action" => "view"]),
  'data-blogContentId' => $blogContent->id
]);
?>


<?php echo $this->BcAdminForm->control('blog_content_id', ['type' => 'hidden', 'value' => $blogContent->id]) ?>
<?php echo $this->BcAdminForm->hidden('mode') ?>
<?php if (empty($blogContent->use_content)): ?>
  <?php echo $this->BcAdminForm->hidden('content') ?>
<?php endif ?>

<!-- form -->
<section class="bca-section">
  <table id="FormTable" class="form-table bca-form-table">
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('title', __d('baser', 'タイトル')) ?>
        &nbsp;<span class="required bca-label" data-bca-label-type="required">
          <?php echo __d('baser', '必須') ?>
        </span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('title', [
          'type' => 'text',
          'size' => 80,
          'maxlength' => 255,
          'autofocus' => true,
          'data-input-text-size' => 'full-counter',
          'counter' => true
        ]) ?>
        <?php echo $this->BcAdminForm->error('title') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('name', __d('baser', 'スラッグ')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('name', [
          'type' => 'text',
          'size' => 40,
          'maxlength' => 255,
          'autofocus' => true,
          'counter' => true
        ]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <?php echo __d('baser', 'スラッグはURLで利用します。スラッグを入力すると、次のようなURLでアクセスできますが入力しない場合は記事NOを利用します。<br>/news/archives/slag') ?>
        </div>
        <?php echo $this->BcAdminForm->error('name') ?>
      </td>
    </tr>
    <?php if ($categories): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('blog_category_id', __d('baser', 'カテゴリー')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <?php echo $this->BcAdminForm->control('blog_category_id', [
            'type' => 'select',
            'options' => $categories,
            'escape' => true
          ]) ?>&nbsp
          <?php if ($hasNewCategoryAddablePermission): ?>
            <?php echo $this->BcAdminForm->button(__d('baser', '新しいカテゴリを追加'), ['id' => 'BtnAddBlogCategory', 'class' => 'bca-btn']) ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->error('blog_category_id') ?>
        </td>
      </tr>
    <?php endif ?>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('eye_catch', __d('baser', 'アイキャッチ画像')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('eye_catch', [
          'type' => 'file',
          'imgsize' => 'thumb',
          'width' => '300'
        ]) ?>
        <?php echo $this->BcAdminForm->error('eye_catch') ?>
      </td>
    </tr>
  </table>
</section>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php if (!empty($blogContent->use_content)): ?>
  <section class="bca-section bca-section__post-content">
    <label for="BlogPostContentTmp"
           class="bca-form-table__label -label"><?php echo $this->BcAdminForm->label('content', __d('baser', '概要')) ?></label>
    <span class="bca-form-table__input-wrap">
	  <?php echo $this->BcAdminForm->ckeditor('content', [
      'editorWidth' => 'auto',
      'editorHeight' => '120px',
      'editorToolType' => 'simple',
      'editorEnterBr' => $editorEnterBr
    ]); ?>
    <?php echo $this->BcAdminForm->error('content') ?>
   </span>
  </section>
<?php endif ?>

<section class="bca-section bca-section__post-detail">
  <label for="BlogPostDetailTmp" class="bca-form-table__label -label">本文</label>
  <span class="bca-form-table__input-wrap">
  <?php echo $this->BcAdminForm->editor('detail', array_merge([
    'type' => 'editor',
    'editor' => $editor,
    'editorUseDraft' => true,
    'editorDraftField' => 'detail_draft',
    'editorWidth' => 'auto',
    'editorHeight' => '480px',
    'editorEnterBr' => $editorEnterBr
  ], $editorOptions)) ?>
  <?php echo $this->BcAdminForm->error('detail') ?>
  </span>
</section>

<section class="bca-section">
  <table class="form-table bca-form-table">
    <?php if (!empty($blogContent->tag_use)): ?>
      <tr>
        <th class="col-head bca-form-table__label">
          <?php echo $this->BcAdminForm->label('blog_tags._ids', __d('baser', 'タグ')) ?>
        </th>
        <td class="col-input bca-form-table__input">
          <div id="BlogTags" class="bca-form-table__group bca-blogtags">
            <?php echo $this->BcAdminForm->control('blog_tags._ids', [
              'type' => 'multiCheckbox',
              'options' => $this->BcAdminForm->getControlSource('BlogPosts.blog_tag_id')
            ]); ?>
            <?php echo $this->BcAdminForm->error('blog_tags._ids') ?>
          </div>
          <?php if ($hasNewTagAddablePermission): ?>
            <div class="bca-form-table__group">
              <?php echo $this->BcAdminForm->control('blog_tag_name', [
                'type' => 'text'
              ]) ?>
              <?php echo $this->BcAdminForm->button(__d('baser', '新しいタグを追加'), [
                'id' => 'BtnAddBlogTag',
                'class' => 'bca-btn'
              ]) ?>
            </div>
          <?php endif ?>
        </td>
      </tr>
    <?php endif ?>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('status', __d('baser', '公開状態')) ?>
        &nbsp;<span class="required bca-label" data-bca-label-type="required">
          <?php echo __d('baser', '必須') ?>
        </span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('status', [
          'type' => 'radio',
          'options' => [0 => __d('baser', '非公開'), 1 => __d('baser', '公開')]
        ]) ?>
        <?php echo $this->BcAdminForm->error('status') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('status', __d('baser', '公開日時')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <span class="bca-datetimepicker__group">
          <span class="bca-datetimepicker__start">
            <?php echo $this->BcAdminForm->control('publish_begin', [
              'type' => 'dateTimePicker',
              'size' => 12,
              'maxlength' => 10,
              'dateLabel' => ['text' => '開始日付'],
              'timeLabel' => ['text' => '開始時間']
            ], true) ?>
          </span>
          <span class="bca-datetimepicker__delimiter">〜</span>
          <span class="bca-datetimepicker__end">
            <?php echo $this->BcAdminForm->control('publish_end', [
              'type' => 'dateTimePicker',
              'size' => 12,
              'maxlength' => 10,
              'dateLabel' => ['text' => '終了日付'],
              'timeLabel' => ['text' => '終了時間']
            ], true) ?>
            </span>
        </span>
        <?php echo $this->BcAdminForm->error('publish_begin') ?>
        <?php echo $this->BcAdminForm->error('publish_end') ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('status', __d('baser', 'サイト内検索')) ?>
        &nbsp
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('exclude_search', [
          'type' => 'checkbox',
          'label' => __d('baser', 'サイト内検索の検索結果より除外する')
        ]) ?>
      </td>
    </tr>
    <tr>
      <th
        class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('user_id', __d('baser', '作成者')) ?>
        &nbsp;<span class="required bca-label" data-bca-label-type="required">
          <?php echo __d('baser', '必須') ?>
        </span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php if (\BaserCore\Utility\BcUtil::isAdminUser()): ?>
          <?php echo $this->BcAdminForm->control('user_id', [
            'type' => 'select',
            'options' => $users
          ]); ?>
          <?php echo $this->BcAdminForm->error('user_id') ?>
        <?php else: ?>
          <?php if (isset($users[$this->BcAdminForm->getSourceValue('user_id')])): ?>
            <?php echo h($users[$this->BcAdminForm->getSourceValue('user_id')]) ?>
          <?php endif ?>
          <?php echo $this->BcAdminForm->hidden('user_id') ?>
        <?php endif ?>
      </td>
    </tr>
    <tr>
      <th class="col-head bca-form-table__label">
        <?php echo $this->BcAdminForm->label('posted', __d('baser', '投稿日時')) ?>
        &nbsp;<span class="required bca-label" data-bca-label-type="required">
          <?php echo __d('baser', '必須') ?>
        </span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('posted', [
          'type' => 'dateTimePicker',
          'size' => 12,
          'maxlength' => 10
        ], true) ?>
        <?php echo $this->BcAdminForm->error('posted') ?>
      </td>
    </tr>
    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</section>

<?php echo $this->BcFormTable->dispatchAfter() ?>


<div id="AddBlogCategoryForm" title="<?php echo __d('baser', 'カテゴリ新規追加') ?>" style="display:none">
  <dl>
    <dt><?php echo $this->BcAdminForm->label('BlogCategory.title', __d('baser', 'カテゴリタイトル')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('BlogCategory.title', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'value' => '', 'autofocus' => true]) ?>
    </dd>
    <dt><?php echo $this->BcAdminForm->label('BlogCategory.name', __d('baser', 'カテゴリ名')) ?></dt>
    <dd>
      <?php echo $this->BcAdminForm->control('BlogCategory.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'value' => '']) ?>
      <i class="bca-icon--question-circle bca-help"></i>
      <div class="bca-helptext">
        <ul>
          <li><?php echo __d('baser', 'URLに利用されます') ?></li>
          <li><?php echo __d('baser', '半角のみで入力してください') ?></li>
          <li><?php echo __d('baser', '空の場合はカテゴリタイトルから値が自動で設定されます') ?></li>
        </ul>
      </div>
    </dd>
  </dl>
</div>
