<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] ウィジェットエリア
 *
 * no を引き数で渡して利用する
 * <?php $this->BcBaser->element('widget_areas',array('no'=>1)) ?>
 */
include BASER_VIEWS . 'Elements' . DS . 'widget_area' . $this->ext;
