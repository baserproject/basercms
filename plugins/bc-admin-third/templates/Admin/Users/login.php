<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * login
 * @var \BaserCore\View\AppView $this
 */
?>


<div class="users form">
<?= $this->BcAdminForm->create() ?>
    <fieldset>
        <legend><?= __('Add User') ?></legend>
        <?= $this->BcAdminForm->control('email', ['type' => 'text']) ?>
        <?= $this->BcAdminForm->control('password', ['type' => 'password']) ?>
   </fieldset>
<?= $this->BcAdminForm->button(__d('baser', 'ログイン'),
         ['div' => false,
         'class' => 'button bca-btn bca-actions__item',
         'data-bca-btn-type' => 'save',
         'data-bca-btn-size' => 'lg',
         'data-bca-btn-width' => 'lg',
         'id' => 'BtnSave']
); ?>
<?= $this->BcAdminForm->end() ?>
</div>
