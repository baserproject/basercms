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
 * [ADMIN] ブログ記事コメント 一覧　行
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcBlog\Model\Entity\BlogContent $blogContent
 * @var \BcBlog\Model\Entity\BlogComment $blogComment
 * @var \BcBlog\Model\Entity\BlogPost $blogPost
 * @checked
 * @noTodo
 * @unitTest
 */
if (!$blogComment->status) {
  $class = ' class="disablerow unpublish"';
} else {
  $class = ' class="publish"';
}
?>


<tr<?php echo $class; ?>>
  <td class="row-tools bca-table-listup__tbody-td">
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $blogComment->id, [
        'type' => 'checkbox',
        'label' => '<span class="bca-visually-hidden">チェックする</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'value' => $blogComment->id,
        'escape' => false
      ]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo $blogComment->no ?></td>
  <td class="bca-table-listup__tbody-td">
      <?php echo h($blogComment->name) ?>
  </td>
  <td class="bca-table-listup__tbody-td">
    <?php if (!empty($blogComment->email)): ?>
      <?php $this->BcBaser->link($blogComment->email, 'mailto:' . $blogComment->email, ['escape' => true]) ?>
    <?php endif; ?>
    <br>
    <?php if($blogComment->url): ?>
    <?php echo $this->BcText->autoLinkUrls($blogComment->url, ['target' => '_blank']) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td">
    <strong>
      <?php $this->BcBaser->link($blogComment->blog_post->name, ['controller' => 'blog_posts', 'action' => 'edit', $blogContent->id, $blogComment->blog_post->id]) ?>
    </strong>
    <br>
    <?php echo nl2br($this->BcText->autoLinkUrls($blogComment->message)) ?>
  </td>

  <?php echo $this->BcListTable->dispatchShowRow($blogComment) ?>

  <td class="bca-table-listup__tbody-td" style="white-space: nowrap">
    <?php echo $this->BcTime->format($blogComment->created); ?><br/>
    <?php echo $this->BcTime->format($blogComment->modified); ?>
  </td>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php if ($blogPost): ?>
      <?php if($blogComment->status): ?>
        <?= $this->BcAdminForm->postLink('', ['action' => 'unpublish', $blogContent->id, $blogComment->id, '?' => ['blog_post_id' => $blogComment->blog_post_id]], [
          'title' => __d('baser_core', '非公開'),
          'class' => 'btn-unpublish bca-btn-icon',
          'data-bca-btn-type' => 'unpublish',
          'data-bca-btn-size' => 'lg'
        ]) ?>
        <?php else: ?>
        <?= $this->BcAdminForm->postLink('', ['action' => 'publish', $blogContent->id, $blogComment->id, '?' => ['blog_post_id' => $blogComment->blog_post_id]], [
          'title' => __d('baser_core', '公開'),
          'class' => 'btn-publish bca-btn-icon',
          'data-bca-btn-type' => 'publish',
          'data-bca-btn-size' => 'lg'
        ]) ?>
        <?php endif ?>
        <?= $this->BcAdminForm->postLink('', ['action' => 'delete', $blogContent->id, $blogComment->id, '?' => ['blog_post_id' => $blogComment->blog_post_id]], [
          'confirm' => __d('baser_core', "{0} を本当に削除してもいいですか？", $blogComment->name),
          'title' => __d('baser_core', '非公開'),
          'class' => 'btn-delete bca-btn-icon',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'lg'
        ]) ?>
    <?php else: ?>
      <?php if($blogComment->status): ?>
        <?= $this->BcAdminForm->postLink('', ['action' => 'unpublish', $blogContent->id, $blogComment->id], [
          'title' => __d('baser_core', '非公開'),
          'class' => 'btn-unpublish bca-btn-icon',
          'data-bca-btn-type' => 'unpublish',
          'data-bca-btn-size' => 'lg'
        ]) ?>
        <?php else: ?>
        <?= $this->BcAdminForm->postLink('', ['action' => 'publish', $blogContent->id, $blogComment->id], [
          'title' => __d('baser_core', '公開'),
          'class' => 'btn-publish bca-btn-icon',
          'data-bca-btn-type' => 'publish',
          'data-bca-btn-size' => 'lg'
        ]) ?>
        <?php endif ?>
        <?= $this->BcAdminForm->postLink('', ['action' => 'delete', $blogContent->id, $blogComment->id], [
          'confirm' => __d('baser_core', "{0} を本当に削除してもいいですか？", $blogComment->name),
          'title' => __d('baser_core', '非公開'),
          'class' => 'btn-delete bca-btn-icon',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'lg'
        ]) ?>
    <?php endif ?>
  </td>
</tr>
