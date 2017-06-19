<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] 統合コンテンツ一覧
 */
$currentUser = BcUtil::loginUser('admin');
$this->BcBaser->js('admin/vendors/jquery.jstree-3.3.1/jstree.min', false);
$this->BcBaser->js('admin/contents/index', false, [
	'id' => 'AdminContentsIndexScript',
	'data-isAdmin' => BcUtil::isAdminUser(),
	'data-isUseMoveContents' => (bool) $currentUser['UserGroup']['use_move_contents'],
	'data-adminPrefix' => BcUtil::getAdminPrefix(),
	'data-editInIndexDisabled' => (bool) $editInIndexDisabled
]);
$this->BcBaser->js('admin/libs/jquery.bcTree', false);
$this->BcBaser->js(array(
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config'
));
echo $this->BcForm->input('BcManageContent', array('type' => 'hidden', 'value' => $this->BcContents->getJsonSettings()));
?>


<script type="text/javascript">

</script>

<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none"><div id="flashMessage" class="notice-message"></div></div>

<?php $this->BcBaser->element('contents/index_view_setting') ?>

<div id="DataList">&nbsp;</div>


