<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * カスタムテーブル編集
 *
 * @var \BcCustomContent\View\CustomContentAdminAppView $this
 * @var \BcCustomContent\Model\Entity\CustomTable $entity
 * @var \Cake\ORM\ResultSet $customLinks
 *
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', '{0}｜テーブル編集', $entity->title));
?>


<div id="AdminCustomTable">

  <?php echo $this->BcAdminForm->create($entity) ?>

  <?php $this->BcBaser->element('CustomTables/form') ?>

  <div class="bca-actions">
    <div class="bca-actions__main">
      <?php $this->BcBaser->link(__d('baser_core', '一覧に戻る'),
        ['action' => 'index'], [
          'class' => 'button bca-btn',
          'data-bca-btn-type' => 'back-to-list'
        ]) ?>
      &nbsp;&nbsp;
      <?php $this->BcBaser->link(__d('baser_core', 'エントリー登録画面確認'),
        ['controller' => 'CustomEntries', 'action' => 'add', $entity->id], [
          'class' => 'button bca-btn',
          'target' => '_blank'
        ]) ?>&nbsp;&nbsp;
      <?php echo $this->BcAdminForm->submit(__d('baser_core', '保存'), [
        'id' => 'BtnSave',
        'div' => false,
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
      ]) ?>
    </div>
    <div class="bca-actions__sub">
      <?php echo $this->BcAdminForm->postLink(__d('baser_core', '削除'),
        ['action' => 'delete', $entity->id], [
          'block' => true,
          'confirm' => __d('baser_core', "{0} を本当に削除してもいいですか？\n\n関連するエントリーやフィールドは全て削除されますのでご注意ください。", $entity->title),
          'class' => 'bca-submit-token button bca-btn bca-actions__item',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'sm',
          'data-bca-btn-color' => 'danger'
        ]) ?>
    </div>
  </div>

  <?php echo $this->BcAdminForm->end() ?>

  <modal ref="modalCustomLinkDetail" :scrollable="true" @modal-opened="linkDetailOpened" @modal-closed="closeLinkDetail">

    <?php $this->BcBaser->element('CustomLinks/form') ?>

    <template slot="footer">
      <button class="bca-btn" type="button" @click="$refs.modalCustomLinkDetail.closeModal()">キャンセル</button>&nbsp;
      <button class="bca-btn" type="button" @click="saveLink">保存</button>
    </template>

  </modal>

  <?php $this->BcBaser->element('CustomLinks/preview') ?>

</div>

<?php echo $this->fetch('postLink') ?>
