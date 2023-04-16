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
 * サブサイト新規登録
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Site $site
 */
$this->BcAdmin->setTitle(__d('baser_core', 'サイト新規登録'));
$this->BcAdmin->setHelp('sites_form');
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser_core', "サブサイトを削除してもよろしいですか？\nサブサイトに関連しているコンテンツは全てゴミ箱に入ります。"),
  'confirmMessage2' => __d('baser_core', 'エイリアスを本当に変更してもいいですか？<br><br>エイリアスを変更する場合、サイト全体のURLが変更となる為、保存に時間がかかりますのでご注意ください。'),
  'confirmTitle1' => __d('baser_core', 'エイリアス変更')
]);
$this->BcBaser->js('admin/sites/form.bundle', false);
?>


<?php echo $this->BcAdminForm->create($site, ['novalidate' => true]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php $this->BcBaser->element('Sites/form') ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
  <div class="bca-actions__before">
    <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'),
      ['admin' => true, 'controller' => 'sites', 'action' => 'index'],
      [
        'class' => 'button bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]
    ) ?>
  </div>
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'id' => 'BtnSave'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
