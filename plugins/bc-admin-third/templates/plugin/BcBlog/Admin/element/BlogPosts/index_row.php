<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログ記事 一覧　行
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $post
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @checked
 * @noTodo
 * @unitTest
 */

use Cake\Utility\Hash;

?>


<tr<?php $this->BcListTable->rowClass($this->Blog->allowPublish($post), $post) ?>>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--select"><?php // 選択 ?>
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $post->id, [
        'type' => 'checkbox',
        'label' => '<span class="bca-visually-hidden">' . __d('baser_core', 'チェックする') . '</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'value' => $post->id,
        'escape' => false
      ]) ?>
    <?php endif ?>
  </td>
  <td
    class="bca-table-listup__tbody-td bca-table-listup__tbody-td--no"><?php // No ?><?php echo $post->no; ?></td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--title"><?php // アイキャッチ＋タイトル ?>
    <div class="eye_catch-wrap">
      <?php if (!empty($post->eye_catch)): ?>
        <div
          class="eye_catch"><?php echo $this->BcUpload->uploadImage('eye_catch', $post, ['imgsize' => 'thumb']) ?></div>
      <?php endif; ?>
      <?php $this->BcBaser->link($post->title, ['action' => 'edit', $blogContent->id, $post->id]) ?>
    </div>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--category"><?php // カテゴリ ?>
    <?php if (!empty($post->blog_category->title)): ?>
      <?php echo h($post->blog_category->title) ?>
    <?php endif; ?>
  </td>

  <?php if ($post->blog_content->tag_use): ?>
    <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--tag"><?php // タグ ?>
      <?php if (!empty($post->blog_tags)): ?>
        <?php $tags = Hash::extract($post->blog_tags, '{n}.name') ?>
        <span class="tag"><?php echo implode('</span><span class="tag">', h($tags)) ?></span>
      <?php endif ?>
    </td>
  <?php endif ?>

  <?php if ($post->blog_content->comment_use): ?>
    <td class="bca-table-listup__tbody-td"><?php // コメント ?>
      <?php
      $comment = 0;
      if($post->blog_comments) $comment = count($post->blog_comments);
      ?>
      <?php if ($comment): ?>
        <?php $this->BcBaser->link($comment, [
          'controller' => 'blog_comments',
          'action' => 'index',
          $post->blog_content->id,
          '?' => ['blog_post_id' => $post->id]
        ]) ?>
      <?php else: ?>
        <?php echo $comment ?>
      <?php endif ?>
    </td>
  <?php endif ?>

  <td class="bca-table-listup__tbody-td"><?php // 作者 ?>
    <?php echo ($post->user)? h($this->BcBaser->getUserName($post->user)) : null ?>
  </td>

  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--date"><?php // 投稿日 ?>
    <?php echo $this->BcTime->format($post->posted, 'yyyy-MM-dd'); ?>
  </td>

  <?php echo $this->BcListTable->dispatchShowRow($post) ?>

  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions"><?php // アクション ?>
    <?php if ($post->status): ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'unpublish', $post->blog_content->id, $post->id],
      [
        'title' => __d('baser_core', '非公開'),
        'class' => 'btn-unpublish bca-btn-icon bca-loading',
        'data-bca-btn-type' => 'unpublish',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?php else: ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'publish', $post->blog_content->id, $post->id],
      [
        'title' => __d('baser_core', '公開'),
        'class' => 'btn-publish bca-btn-icon bca-loading',
        'data-bca-btn-type' => 'publish',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?php endif ?>
    <?php $this->BcBaser->link('',
      $this->request->getAttribute('currentContent')->url . '/archives/' . $post->no,
      ['title' => __d('baser_core', '確認'), 'target' => '_blank', 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'preview', 'data-bca-btn-size' => 'lg']
    ) ?>
    <?php $this->BcBaser->link('',
      ['action' => 'edit', $post->blog_content->id, $post->id],
      ['title' => __d('baser_core', '編集'), 'class' => ' bca-btn-icon', 'data-bca-btn-type' => 'edit', 'data-bca-btn-size' => 'lg']
    ) ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'copy', $post->blog_content->id, $post->id],
      [
        'title' => __d('baser_core', 'コピー'),
        'class' => 'btn-copy bca-btn-icon bca-loading',
        'data-bca-btn-type' => 'copy',
        'data-bca-btn-size' => 'lg']
    ) ?>
    <?= $this->BcAdminForm->postLink(
      '',
      ['action' => 'delete', $post->blog_content->id, $post->id],
      [
        'confirm' => __d('baser_core', "{0} を本当に削除してもいいですか？", $post->title),
        'title' => __d('baser_core', '削除'),
        'class' => 'btn-delete bca-btn-icon bca-loading',
        'data-bca-btn-type' => 'delete',
        'data-bca-btn-size' => 'lg']
    ) ?>
  </td>
</tr>
