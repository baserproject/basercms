<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
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
