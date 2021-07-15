<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\View\{AppView as AppViewAlias};
use BaserCore\Model\Entity\EntityInterface;

/**
 * Permissions Add
 * @var AppViewAlias $this
 * @var Permission $permission
 */
?>


<?= $this->BcAdminForm->create($permission, ['novalidate' => true]) ?>

<?php $this->BcBaser->element('Permissions/form') ?>

<div class="submit section bca-actions">
    <div class="bca-actions__main">
        <?= $this->BcAdminForm->button(
                __d('baser', '保存'),
                 ['div' => false,
                 'class' => 'button bca-btn bca-actions__item',
                 'data-bca-btn-type' => 'save',
                 'data-bca-btn-size' => 'lg',
                 'data-bca-btn-width' => 'lg',
                 'id' => 'BtnSave']
            ) ?>
    </div>
</div>

<?= $this->BcAdminForm->end() ?>
