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
 * サブサイト編集
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Site $site
 */
$this->BcAdmin->setTitle(__d('baser', 'サイト編集'));
$this->BcAdmin->setHelp('sites_form');
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser', "サブサイトを削除してもよろしいですか？\nサブサイトに関連しているコンテンツは全てゴミ箱に入ります。"),
  'confirmMessage2' => __d('baser', 'エイリアスを本当に変更してもいいですか？<br><br>エイリアスを変更する場合、サイト全体のURLが変更となる為、保存に時間がかかりますのでご注意ください。'),
  'confirmTitle1' => __d('baser', 'エイリアス変更')
], ['escape' => false]);
$this->BcBaser->js('admin/sites/form.bundle', false);
?>


<?php echo $this->BcAdminForm->create($site) ?>

<?php $this->BcBaser->element('Sites/form', ['siteListOptions' => ['excludeIds' => $site->id]]) ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg',]) ?>
  </div>
  <?php if(!$this->BcAdminSite->isMainOnCurrentDisplay($site)): ?>
  <div class="bca-actions__sub">
      <?= $this->BcAdminForm->postLink(
        __d('baser', '削除'),
        ['action' => 'delete', $site->id],
        ['block' => true,
          'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $site->display_name),
          'class' => 'submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm']
      ) ?>
  </div>
  <?php endif ?>
</div>
<?php echo $this->BcHtml->link(__d('baser', '一覧に戻る'), ['action' => 'index'], ['class' => 'button bca-btn', 'data-bca-btn-type' => 'back-to-list']) ?>

<?php echo $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>
