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

use BaserCore\View\AppView;

/**
 * @var AppView $this
 */
?>

<div class="section">
	<p><?php echo __d('baser', 'パスワードを忘れた方は、登録されているメールアドレスを送信してください。<br />パスワードの再発行URLをメールでお知らせします。')?></p>

	<?= $this->BcAdminForm->create($passwordRequest, ['novalidate' => true]) ?>
	<div class="submit">
        <p>
            <?php echo $this->BcAdminForm->control('email', ['type' => 'text', 'size' => '50', 'maxlength' => 255, 'placeholder' => 'yourname@example.com']) ?>
        </p>

        <?= $this->BcAdminForm->button(
                __d('baser', '保存'),
                 ['div' => false,
                 'class' => 'button bca-btn bca-actions__item',
                 'data-bca-btn-type' => 'save',
                 'data-bca-btn-size' => 'lg',
                 'data-bca-btn-width' => 'lg',
                 'id' => 'BtnSave']
            ) ?>
        <?php echo $this->BcAdminForm->error('email') ?>
	</div>
    <?= $this->BcAdminForm->end() ?>
</div>
<script>
(function(){
    document.getElementById('email').focus();
})();
</script>
