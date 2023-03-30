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
 * [ADMIN] ウィジェットエリア編集
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcWidgetArea\Model\Entity\WidgetArea $widgetArea
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', '新規ウィジェットエリア登録'));
$this->BcAdmin->setHelp('widget_areas_form');
?>


<?php echo $this->BcAdminForm->create($widgetArea, ['url' => ['action' => 'add']]) ?>
<?php echo $this->BcAdminForm->label('name', __d('baser_core', 'ウィジェットエリア名')) ?>&nbsp;
<?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 40, 'autofocus' => true]) ?>&nbsp;
<?php echo $this->BcAdminForm->submit(__d('baser_core', 'エリア名を保存する'), [
    'div' => false,
    'class' => 'button bca-btn',
    'id' => 'WidgetAreaUpdateTitleSubmit',
    'data-bca-btn-type' => 'save'
]) ?>
<?php $this->BcBaser->img('admin/ajax-loader-s.gif', ['style' => 'display:none', 'class' => 'bca-small-loader', 'id' => 'WidgetAreaUpdateTitleLoader']) ?>
<?php echo $this->BcAdminForm->error('name') ?>
<?php echo $this->BcAdminForm->end() ?>


