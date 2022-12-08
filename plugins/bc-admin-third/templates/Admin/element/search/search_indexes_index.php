<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

use BaserCore\Utility\BcUtil;
use BaserCore\View\BcAdminAppView;

/**
 * [ADMIN] 検索インデックス一覧検索ボックス
 *
 * @var BcAdminAppView $this
 * @var array $sites
 * @var array $folders
 * @var \BcSearchIndex\Form\SearchIndexesSearchForm $searchIndexesSearch
 */
$priorities = [
    '0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5',
    '0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1.0' => '1.0'
];
$types = BcUtil::unserialize($this->BcSiteConfig->getValue('content_types'));
?>


<?php echo $this->BcAdminForm->create($searchIndexesSearch, ['type' => 'get', 'url' => ['action' => 'index']]) ?>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('type', __d('baser', 'タイプ'), ['class' => 'bca-search__input-item-label']) ?>
        <?php echo $this->BcAdminForm->control('type', ['type' => 'select', 'options' => $types, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
    <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('site_id', __d('baser', 'サブサイト'), ['class' => 'bca-search__input-item-label']) ?>
        <?php echo $this->BcAdminForm->control('site_id', ['type' => 'select', 'options' => $sites]) ?>
	</span>
    <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('folder_id', __d('baser', 'フォルダ'), ['class' => 'bca-search__input-item-label']) ?>
        <?php echo $this->BcAdminForm->control('folder_id', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
    <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('keyword', __d('baser', 'キーワード'), ['class' => 'bca-search__input-item-label']) ?>
        <?php echo $this->BcAdminForm->control('keyword', ['type' => 'text', 'class' => 'bca-textbox__input', 'size' => '30']) ?>
	</span>
    <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('status', __d('baser', '公開状態'), ['class' => 'bca-search__input-item-label']) ?>
        <?php echo $this->BcAdminForm->control('status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定なし')]) ?>
	</span>
    <span class="bca-search__input-item">
		<?php echo $this->BcAdminForm->label('priority', __d('baser', '優先度'), ['class' => 'bca-search__input-item-label']) ?>
        <?php echo $this->BcAdminForm->control('priority', ['type' => 'select', 'options' => $priorities, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
    <?php echo $this->BcSearchBox->dispatchShowField() ?>
</p>
<div class="button bca-search__btns">
    <div class="bca-search__btns-item">
        <?php echo $this->BcAdminForm->button(__d('baser', '検索'), ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn bca-loading', 'data-bca-btn-type' => 'search']) ?>
    </div>
    <div class="bca-search__btns-item">
        <?php echo $this->BcAdminForm->button(__d('baser', 'クリア'), ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?>
    </div>
</div>
<?php echo $this->BcAdminForm->end() ?>
