<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * ウィジェットエリア
 *
 * BcBaserHelper::widgetArea() で呼び出す
 * <?php $this->BcBaser->widgetArea() ?>
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var int $no ウィジェットエリアID
 * @checked
 * @noTodo
 * @unitTest
 **/
if ($this->getRequest()->is('maintenance') || empty($no)) return;
?>


<div class="bs-widget-area bs-widget-area-<?php echo $no ?>">
	<?php $this->BcWidgetArea->show($no) ?>
</div>
