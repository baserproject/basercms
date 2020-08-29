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
use BaserCore\Model\Entity\UserGroup;

/**
 * UserGroups Add
 * @var AppViewAlias $this
 * @var UserGroup $userGroup
 * @var bool $editable
 * @var bool $deletable
 */
?>


<?= $this->BcAdminForm->create($userGroup, ['novalidate' => true]) ?>

<? $this->BcBaser->element('Admin/UserGroups/form') ?>

<div class="submit bc-align-center section bca-actions">
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
    <div class="bca-actions__sub">
    <?php if ($userGroup->name != 'admins'): ?>
        <?= $this->BcAdminForm->postLink(
            __d('baser', '削除'),
            ['action' => 'delete', $userGroup->id],
            ['block' => true,
            'confirm' => __d('baser', "{0} を本当に削除してもいいですか？\n\n削除する場合、関連するユーザーは削除されませんが、関連するアクセス制限設定は全て削除されます。\n※ 関連するユーザーは管理者グループに所属する事になります。", $userGroup->name),
            'class' => 'submit-token button bca-btn bca-actions__item',
            'data-bca-btn-type' => 'delete',
            'data-bca-btn-size' => 'sm']
        ) ?>
    <?php endif; ?>
    </div>
</div>

<?= $this->BcAdminForm->end() ?>

<?= $this->fetch('postLink') ?>

<?php /* ?>
<?php if ($this->request->getParam('action') == 'edit'): ?>
	<div class="section">
		<div class="panel-box bca-panel-box corner10">
			<h2><?php echo __d('baser', '「よく使う項目」の初期データ') ?></h2>
			<p>
				<small><?php echo __d('baser', 'このグループに新しいユーザーを登録した際、次の「よく使う項目」が登録されます。	') ?></small>
			</p>
			<?php $favorites = BcUtil::unserialize($this->request->data['UserGroup']['default_favorites']) ?>
			<?php if ($favorites): ?>
			<ul class="bca-list" data-bca-list-layout="horizon">
				<?php foreach ($favorites as $favorite): ?>
					<li class="bca-list__item"><?php $this->BcBaser->link($favorite['name'], $favorite['url']) ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif ?>
		</div>
	</div>
<?php endif; ?>
<?php */ ?>
