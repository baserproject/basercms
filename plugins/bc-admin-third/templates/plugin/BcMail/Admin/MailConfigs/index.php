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
 * [ADMIN] メール設定 フォーム
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcMail\Model\Entity\MailConfig $entity
 */
$this->BcAdmin->setTitle(__d('baser_core', 'メールプラグイン基本設定'));
$this->BcAdmin->setHelp('mail_configs_form');
?>


<h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', '送信メール署名') ?></h2>

<!-- form -->
<?php echo $this->BcAdminForm->create($entity) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
  <table id="FormTable" class="form-table bca-form-table">
    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->BcAdminForm->label('site_name', __d('baser_core', 'Webサイト名')) ?>
        &nbsp;<span class="required bca-label" data-bca-label-type="required"><?php echo __d('baser_core', '必須') ?></span>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('site_name', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'autofocus' => true]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext"><?php echo __d('baser_core', '自動送信メールの署名に挿入されます。') ?></div>
        <?php echo $this->BcAdminForm->error('site_name') ?>
      </td>
    </tr>
    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->BcAdminForm->label('site_url', __d('baser_core', 'WebサイトURL')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('site_url', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext"><?php echo __d('baser_core', '自動送信メールの署名に挿入されます。') ?></div>
        <?php echo $this->BcAdminForm->error('site_url') ?>
      </td>
    </tr>
    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->BcAdminForm->label('site_email', __d('baser_core', 'Eメール')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('site_email', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext">
          <ul>
            <li><?php echo __d('baser_core', '自動送信メールの署名に挿入されます。') ?></li>
            <li><?php echo __d('baser_core', 'メールの送信先ではありません。') ?></li>
          </ul>
        </div>
        <?php echo $this->BcAdminForm->error('site_email') ?>
      </td>
    </tr>
    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->BcAdminForm->label('site_tel', __d('baser_core', '電話番号')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('site_tel', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext"><?php echo __d('baser_core', '自動送信メールの署名に挿入されます。') ?></div>
        <?php echo $this->BcAdminForm->error('site_tel') ?>
      </td>
    </tr>
    <tr>
      <th class="bca-form-table__label">
        <?php echo $this->BcAdminForm->label('site_fax', __d('baser_core', 'FAX番号')) ?>
      </th>
      <td class="col-input bca-form-table__input">
        <?php echo $this->BcAdminForm->control('site_fax', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
        <i class="bca-icon--question-circle bca-help"></i>
        <div class="bca-helptext"><?php echo __d('baser_core', '自動送信メールの署名に挿入されます。') ?></div>
        <?php echo $this->BcAdminForm->error('site_fax') ?>
      </td>
    </tr>
    <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
  </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item bca-loading',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
