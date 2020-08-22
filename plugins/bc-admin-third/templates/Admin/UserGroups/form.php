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

use BaserCore\Model\Entity\UserGroup;
use BaserCore\View\AppView;

/**
 * UsersGroups form
 * @var AppView $this
 * @var UserGroup $userGroup
 */

$this->BcBaser->js('admin/user_groups/form', false);
$authPrefixes = [];
// TODO
// foreach (Configure::read('BcAuthPrefix') as $key => $authPrefix) {
// 	$authPrefixes[$key] = $authPrefix['name'];
// }
?>


<script type="text/javascript">
/*
$(window).load(function() {
<?php if ($userGroup->name == 'admins'): ?>
	$("#UserGroupAuthPrefixAdmin").prop('disabled', true);
<?php endif ?>
	$("#UserGroupAdminEditForm").submit(function(){
		$("#UserGroupAuthPrefixAdmin").removeAttr('disabled');
	});
});
*/
</script>

<!-- form -->
<?php echo $this->BcAdminForm->create($userGroup) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<div class="section">
    <table id="FormTable" class="form-table bca-form-table">
        <?php if ($this->request->getParam('action') == 'edit'): ?>
            <tr>
                <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('id', 'No') ?></th>
                <td class="col-input bca-form-table__input">
                    <?php echo $userGroup->id; ?>
                    <?php echo $this->BcAdminForm->control('id', ['type' => 'hidden']) ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('name', __d('baser', 'ユーザーグループ名')) ?>&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
            <td class="col-input bca-form-table__input">
                <?php if ($userGroup->name == 'admins' && $this->request->getParam('action') == 'edit'): ?>
                    <?php echo $userGroup->name; ?>
                    <?php echo $this->BcAdminForm->control('name', ['type' => 'hidden']) ?>
                <?php else: ?>
                    <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 20, 'maxlength' => 255, 'autofocus' => true]) ?>
                <?php endif ?>
                <i class="bca-icon--question-circle btn help bca-help"></i>
                <?php echo $this->BcAdminForm->error('name') ?>
                <div id="helptextName" class="helptext">
                    <ul>
                        <li><?php echo __d('baser', '重複しない識別名称を半角のみで入力してください。') ?></li>
                        <li><?php echo __d('baser', 'admins の場合は変更できません。') ?></li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('title', __d('baser', '表示名')) ?>&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
            <td class="col-input bca-form-table__input">
                <?php echo $this->BcAdminForm->control('title', ['type' => 'text', 'size' => 20, 'maxlength' => 255]) ?>
                <i class="bca-icon--question-circle btn help bca-help"></i>
                <div id="helptextTitle" class="helptext"><?php echo __d('baser', '日本語が入力できますのでわかりやすい名称を入力します。') ?></div>
                <?php echo $this->BcAdminForm->error('title') ?>
            </td>
        </tr>
        <tr>
            <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('use_admin_globalmenu', __d('baser', 'その他')) ?></th>
            <td class="col-input bca-form-table__input">
                <?php if ($userGroup->name == 'admins' && $this->request->getParam('action') == 'edit'): ?>
                    <?php if ($userGroup->use_admin_globalmenu): ?>
                        <?php echo __d('baser', '管理システムのグローバルメニューを利用する')?>
                    <?php else: ?>
                        <?php echo __d('baser', '管理システムのグローバルメニューを利用しない')?>
                    <?php endif ?>
                <?php else: ?>
                    <span style="white-space: nowrap"><?php echo $this->BcAdminForm->control('use_admin_globalmenu', ['type' => 'checkbox', 'label' => __d('baser', '管理システムのグローバルメニューを利用する')]) ?></span>
                    <i class="bca-icon--question-circle btn help bca-help"></i>
                    <div id="helptextName" class="helptext">
                        <ul>
                            <li><?php echo __d('baser', '管理システムでグローバルメニューを利用するかどうか設定します。') ?></li>
                            <li><?php echo __d('baser', '管理グループの場合は変更できません。') ?></li>
                        </ul>
                    </div>
                    <?php echo $this->BcAdminForm->error('use_admin_globalmenu') ?>
                <?php endif ?>
                <br>
                <span style="white-space: nowrap"><?php echo $this->BcAdminForm->control('use_move_contents', ['type' => 'checkbox', 'label' => __d('baser', 'コンテンツのドラッグ＆ドロップ移動機能を利用する')]) ?></span>
                <i class="bca-icon--question-circle btn help bca-help"></i>
                <div id="helptextName" class="helptext">
                    <span><?php echo __d('baser', 'コンテンツ一覧のツリー構造において、ドラッグ＆ドロップでコンテンツの移動を許可するかどうかを設定します。') ?></span>
                </div>
                <?php echo $this->BcAdminForm->error('use_move_contents') ?>
            </td>
        </tr>
        <?php if (count($authPrefixes) > 1): ?>
            <tr>
                <th class="col-head bca-form-table__label"><?php echo $this->BcAdminForm->label('auth_prefix', __d('baser', '認証プレフィックス設定')) ?>&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
                <td class="col-input bca-form-table__input">
                    <?php echo $this->BcAdminForm->control('auth_prefix', ['type' => 'select', 'multiple' => 'checkbox', 'options' => $authPrefixes, 'value' => explode(',', $userGroup->auth_prefix)]) ?>
                    <i class="bca-icon--question-circle btn help bca-help"></i>
                    <?php echo $this->BcAdminForm->error('auth_prefix') ?>
                    <div id="helptextAuthPrefix" class="helptext">
                        <?php echo __d('baser', '認証プレフィックスの設定を指定します。<br />ユーザーグループ名が admins の場合は編集できません。')?>
                    </div>
                </td>
            </tr>
        <?php endif ?>
        <?php echo $this->BcAdminForm->dispatchAfterForm() ?>
    </table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

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
<?php if ($this->request->getParam('action') == 'edit'): ?>
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
<?php endif; ?>
</div>

<?php echo $this->BcAdminForm->end() ?>

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

