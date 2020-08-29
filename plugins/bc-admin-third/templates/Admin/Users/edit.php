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

use BaserCore\View\{AppView as AppViewAlias};
use BaserCore\Model\Entity\User;

/**
 * Users Edit
 * @var AppViewAlias $this
 * @var User $user
 * @var bool $editable
 * @var bool $deletable
 */
?>


<?= $this->BcAdminForm->create($user, ['novalidate' => true]) ?>

<? $this->BcBaser->element('Admin/Users/form') ?>

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
<? if ($editable && $deletable): ?>
    <div class="bca-actions__sub">
        <?= $this->BcAdminForm->postLink(
                __d('baser', '削除'),
                ['action' => 'delete', $user->id],
                ['block' => true,
                'confirm' => __d('baser', '{0} を本当に削除してもいいですか？', $user->name),
                'class' => 'submit-token button bca-btn bca-actions__item',
                'data-bca-btn-type' => 'delete',
                'data-bca-btn-size' => 'sm']
        ) ?>
    </div>
<? endif ?>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>

<?php // TODO: よく使うメニュー関連 ?>
<?php /* if ($this->request->action == 'admin_edit'): ?>
	<div class="panel-box bca-panel-box corner10">
		<h2><?php echo __d('baser', '登録されている「よく使う項目」') ?></h2>
		<?php if ($this->request->data['Favorite']): ?>
			<ul class="bca-list" data-bca-list-layout="horizon" id="DefaultFavorites">
				<?php foreach ($this->request->data['Favorite'] as $key => $favorite): ?>
					<li class="bca-list__item">
						<?php $this->BcBaser->link($favorite['name'], $favorite['url']) ?>
						<?php echo $this->BcAdminForm->control('Favorite.name.' . $key, ['type' => 'hidden', 'value' => $favorite['name'], 'class' => 'favorite-name']) ?>
						<?php echo $this->BcAdminForm->control('Favorite.url.' . $key, ['type' => 'hidden', 'value' => $favorite['url'], 'class' => 'favorite-url']) ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif ?>
		<?php if ($this->Session->check('AuthAgent') || $this->BcBaser->isAdminUser()): ?>
			<div class="submit"><?php echo $this->BcAdminForm->button($this->request->data['UserGroup']['title'] . 'グループの初期値に設定', ['label' => __d('baser', 'グループ初期データに設定'), 'id' => 'btnSetUserGroupDefault', 'class' => 'button bca-btn']) ?></div>
		<?php endif ?>
	</div>
<?php endif*/ ?>
